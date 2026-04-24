<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\Billing\BillCustomizationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->company = app(BillCustomizationService::class)->companyPayload();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice #'.$this->invoice->invoice_number.' - Payment Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'company' => $this->company,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $billCustomizationService = app(BillCustomizationService::class);
        $data = $billCustomizationService->invoiceViewData($this->invoice, [
            'device_type' => 'desktop',
            'input_mode' => 'manual',
            'printer_hint' => 'Email PDF',
        ]);
        $pdf = PDF::loadView('exports.invoice-pdf', $data)
            ->setPaper(
                $billCustomizationService->paperConfig($data['billProfile']),
                $billCustomizationService->paperOrientation($data['billProfile'])
            );

        return [
            Attachment::fromData(fn () => $pdf->output(), 'invoice-'.$this->invoice->invoice_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
