<?php

namespace App\Services;

use App\Models\Invoice;
use Log;

/**
 * Raw ESC/POS thermal receipt printer service for Windows host environment.
 * Generates binary ESC/POS command sequences and sends them
 * directly to a USB/network print port - bypassing the Windows GDI/browser print pipeline.
 */
class EscPosService
{
    // ESC/POS control characters
    const ESC = "\x1B";
    const GS  = "\x1D";
    const LF  = "\x0A";  // Line feed / new line
    const NUL = "\x00";

    /**
     * Generate a complete ESC/POS raw receipt for an invoice.
     */
    public static function buildInvoiceReceipt(Invoice $invoice, array $profile, array $company): string
    {
        $buf = '';

        // ── Initialize printer ──────────────────────────────────────────────
        $buf .= self::ESC . '@';           // ESC @ : Initialize / reset printer

        // ── Center alignment ────────────────────────────────────────────────
        $buf .= self::ESC . 'a' . "\x01"; // ESC a 1 : Center align

        // ── Bold ON ─────────────────────────────────────────────────────────
        $buf .= self::ESC . 'E' . "\x01";

        // ── Double-size header (Company Name) ───────────────────────────────
        $buf .= self::GS . '!' . "\x11"; // GS ! 17 : Width x2, Height x2
        $buf .= strtoupper($company['name']) . self::LF;

        // ── Normal size ─────────────────────────────────────────────────────
        $buf .= self::GS . '!' . "\x00";
        $buf .= self::ESC . 'E' . "\x00"; // Bold OFF

        if ($profile['show_company_phone'] && !empty($company['phone'])) {
            $buf .= 'Tel: ' . $company['phone'] . self::LF;
        }
        if (!empty($company['email'])) {
            $buf .= $company['email'] . self::LF;
        }
        if (!empty($company['address'])) {
            $buf .= $company['address'] . self::LF;
        }
        if ($profile['show_tax_id'] && !empty($company['tax_id']) && $company['tax_id'] !== 'N/A') {
            $buf .= 'Tax ID: ' . $company['tax_id'] . self::LF;
        }
        if (!empty($profile['header_note'])) {
            $buf .= $profile['header_note'] . self::LF;
        }
        $buf .= self::LF;

        // Determine paper character width: 80mm typically fits 40 chars; 58mm fits 32 chars
        $paperWidth = $profile['paper_size'] ?? 'thermal_80';
        $charWidth = ($paperWidth === 'thermal_58') ? 32 : 40;

        // ── Separator ───────────────────────────────────────────────────────
        $buf .= str_repeat('-', $charWidth) . self::LF;

        // ── Left align ──────────────────────────────────────────────────────
        $buf .= self::ESC . 'a' . "\x00";

        // ── Receipt meta ────────────────────────────────────────────────────
        $buf .= self::formatLine('INVOICE NO:', $invoice->invoice_number, $charWidth);
        $buf .= self::formatLine('DATE/TIME:', $invoice->invoice_date?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'), $charWidth);
        $buf .= self::formatLine('CUSTOMER:', $invoice->customer_name ?: 'Walk-in customer', $charWidth);

        if ($profile['show_customer_email'] && !empty($invoice->customer_email)) {
            $buf .= self::formatLine('EMAIL:', $invoice->customer_email, $charWidth);
        }
        if ($profile['show_customer_phone'] && !empty($invoice->customer_phone)) {
            $buf .= self::formatLine('PHONE:', $invoice->customer_phone, $charWidth);
        }
        if ($profile['show_customer_address'] && !empty($invoice->customer_address)) {
            $buf .= self::formatLine('ADDRESS:', $invoice->customer_address, $charWidth);
        }

        if ($profile['show_payment_method']) {
            $methodNames = [
                'cash' => 'Cash',
                'card' => 'Credit/Debit Card',
                'bank_transfer' => 'Bank Transfer',
                'mobile_money' => 'Mobile Money',
                'cheque' => 'Cheque',
                'credit' => 'Store Credit',
                'split' => 'Split Payment',
            ];
            $method = $methodNames[$invoice->payment_method] ?? strtoupper((string) $invoice->payment_method);
            $buf .= self::formatLine('PAYMENT:', $method, $charWidth);
        }
        $buf .= str_repeat('-', $charWidth) . self::LF;

        // ── Items header ────────────────────────────────────────────────────
        $buf .= self::ESC . 'E' . "\x01"; // Bold ON
        if ($charWidth === 32) {
            $buf .= self::padRight('ITEM', 18) . self::padLeft('QTY', 5) . self::padLeft('TOTAL', 9) . self::LF;
        } else {
            $buf .= self::padRight('ITEM', 24) . self::padLeft('QTY', 6) . self::padLeft('TOTAL', 10) . self::LF;
        }
        $buf .= self::ESC . 'E' . "\x00"; // Bold OFF
        $buf .= str_repeat('-', $charWidth) . self::LF;

        // ── Items ───────────────────────────────────────────────────────────
        $symbol = $company['currency_symbol'] ?? 'Rs';
        foreach ($invoice->items as $item) {
            $name = $item->item_name;
            $qty = (string) $item->quantity;
            $totalStr = $symbol . ' ' . number_format($item->total, 2);

            if ($charWidth === 32) {
                if (strlen($name) > 18) {
                    $buf .= wordwrap($name, 18, self::LF, true) . self::LF;
                    $buf .= self::padRight('', 18) . self::padLeft($qty, 5) . self::padLeft($totalStr, 9) . self::LF;
                } else {
                    $buf .= self::padRight($name, 18) . self::padLeft($qty, 5) . self::padLeft($totalStr, 9) . self::LF;
                }
            } else {
                if (strlen($name) > 24) {
                    $buf .= wordwrap($name, 24, self::LF, true) . self::LF;
                    $buf .= self::padRight('', 24) . self::padLeft($qty, 6) . self::padLeft($totalStr, 10) . self::LF;
                } else {
                    $buf .= self::padRight($name, 24) . self::padLeft($qty, 6) . self::padLeft($totalStr, 10) . self::LF;
                }
            }
        }

        // ── Totals ──────────────────────────────────────────────────────────
        $buf .= str_repeat('-', $charWidth) . self::LF;
        $buf .= self::formatLine('SUBTOTAL:', $symbol . ' ' . number_format($invoice->subtotal, 2), $charWidth);
        if ($invoice->discount > 0) {
            $buf .= self::formatLine('DISCOUNT:', '-' . $symbol . ' ' . number_format($invoice->discount, 2), $charWidth);
        }
        if ($invoice->tax_amount > 0) {
            $buf .= self::formatLine('TAX:', $symbol . ' ' . number_format($invoice->tax_amount, 2), $charWidth);
        }
        $buf .= str_repeat('=', $charWidth) . self::LF;

        // ── Grand total (bold) ───────────────────────────────────────────────
        $buf .= self::ESC . 'E' . "\x01"; // Bold ON
        $buf .= self::formatLine('TOTAL:', $symbol . ' ' . number_format($invoice->total, 2), $charWidth);
        $buf .= self::ESC . 'E' . "\x00"; // Bold OFF

        // ── Footer ──────────────────────────────────────────────────────────
        $buf .= self::LF;
        $buf .= self::ESC . 'a' . "\x01"; // Center align
        if ($profile['show_notes'] && !empty($invoice->notes)) {
            $buf .= 'Notes: ' . $invoice->notes . self::LF;
        }
        if (!empty($profile['footer_note'])) {
            $buf .= $profile['footer_note'] . self::LF;
        }
        $buf .= self::LF;

        // ── Feed and cut ────────────────────────────────────────────────────
        $buf .= self::LF . self::LF . self::LF;
        $buf .= self::GS . 'V' . "\x42" . "\x00"; // Full paper cut

        return $buf;
    }

    /**
     * Send raw ESC/POS binary to the printer.
     */
    public static function sendToPort(string $rawData, array $printer): array
    {
        try {
            $connectionType = $printer['connection_type'] ?? 'usb';

            if ($connectionType === 'network') {
                // ── Network TCP/IP ────────────────────────────────────────────
                $ip = $printer['ip_address'] ?? '';
                $port = (int) ($printer['port'] ?? 9100);

                if (empty($ip)) {
                    return ['success' => false, 'error' => 'IP Address is not configured.'];
                }

                $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
                if (!$socket) {
                    return ['success' => false, 'error' => "Cannot connect to network printer {$ip}:{$port} — {$errstr}"];
                }
                fwrite($socket, $rawData);
                fclose($socket);
                return ['success' => true];
            } else {
                // ── USB / Shared printer via Windows Spooler WritePrinter API ──
                $printerName = $printer['queue_name'] ?: ($printer['alias'] ?? '');
                
                if (empty($printerName)) {
                    return ['success' => false, 'error' => 'Printer queue/alias is empty.'];
                }

                // Purge any stale browser GDI jobs first so they don't print before us
                self::purgeSpoolerQueue($printerName);

                $tempBin = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'escpos_' . uniqid() . '.bin';
                file_put_contents($tempBin, $rawData);
                $tempBinEsc   = str_replace('\\', '\\\\', $tempBin);
                $printerNameEsc  = str_replace('"', '`"', $printerName);

                $psScript = self::buildWritePrinterScript($printerNameEsc, $tempBinEsc);
                $psFile   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rawprint_' . uniqid() . '.ps1';
                file_put_contents($psFile, $psScript);

                exec('powershell -ExecutionPolicy Bypass -File "' . $psFile . '"', $output, $retCode);

                @unlink($tempBin);
                @unlink($psFile);

                $log = implode(' | ', $output);

                if ($retCode !== 0 || str_contains($log, 'FAILED') || str_contains($log, 'False')) {
                    return ['success' => false, 'error' => 'Print spooler rejected the job. Output: ' . $log];
                }

                return ['success' => true];
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build the PowerShell script that uses the Windows Spooler WritePrinter API.
     */
    private static function buildWritePrinterScript(string $printerName, string $tempBinPath): string
    {
        return <<<PS
Add-Type -TypeDefinition @'
using System;
using System.Runtime.InteropServices;
public class RawPrinter {
    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Ansi)]
    public struct DOC_INFO_1 {
        public string pDocName;
        public string pOutputFile;
        public string pDatatype;
    }
    [DllImport("winspool.drv", EntryPoint="OpenPrinterA", SetLastError=true)]
    public static extern bool OpenPrinter([MarshalAs(UnmanagedType.LPStr)] string name, ref IntPtr hPrinter, IntPtr pd);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool ClosePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", EntryPoint="StartDocPrinterA", SetLastError=true)]
    public static extern int StartDocPrinter(IntPtr hPrinter, int level, ref DOC_INFO_1 di);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool EndDocPrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool StartPagePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool EndPagePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool WritePrinter(IntPtr hPrinter, IntPtr pBytes, int cnt, ref int written);
    public static bool Send(string printerName, byte[] bytes) {
        IntPtr h = IntPtr.Zero;
        if (!OpenPrinter(printerName, ref h, IntPtr.Zero)) { return false; }
        DOC_INFO_1 di = new DOC_INFO_1 { pDocName="POS Receipt", pOutputFile=null, pDatatype="RAW" };
        if (StartDocPrinter(h, 1, ref di) == 0) { ClosePrinter(h); return false; }
        StartPagePrinter(h);
        IntPtr p = Marshal.AllocHGlobal(bytes.Length);
        Marshal.Copy(bytes, 0, p, bytes.Length);
        int w = 0;
        bool ok = WritePrinter(h, p, bytes.Length, ref w);
        Marshal.FreeHGlobal(p);
        EndPagePrinter(h); EndDocPrinter(h); ClosePrinter(h);
        return ok;
    }
}
'@
\$bytes = [System.IO.File]::ReadAllBytes("$tempBinPath")
\$ok = [RawPrinter]::Send("$printerName", \$bytes)
if (\$ok) { Write-Host "PRINT_OK" } else { Write-Host "PRINT_FAILED" }
PS;
    }

    private static function formatLine(string $label, string $value, int $width): string
    {
        $space = $width - strlen($label) - strlen($value);
        if ($space < 1) $space = 1;
        return $label . str_repeat(' ', $space) . $value . self::LF;
    }

    private static function padRight(string $text, int $width): string
    {
        return str_pad($text, $width, ' ', STR_PAD_RIGHT);
    }

    private static function padLeft(string $text, int $width): string
    {
        return str_pad($text, $width, ' ', STR_PAD_LEFT);
    }

    /**
     * Cancel all pending/stale print jobs on the Windows print queue.
     */
    private static function purgeSpoolerQueue(string $printerName): void
    {
        $safe = str_replace("'", "`'", $printerName);
        $cmd  = "powershell -Command \"Get-PrintJob -PrinterName '$safe' -ErrorAction SilentlyContinue | Remove-PrintJob -ErrorAction SilentlyContinue\" 2>nul";
        exec($cmd);
        usleep(300_000); // 300ms pause to settle spooler
    }
}
