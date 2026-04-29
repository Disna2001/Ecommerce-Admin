<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Services\AuditLogService;
use App\Services\Billing\BillCustomizationService;
use App\Services\Inventory\StockMovementService;
use App\Services\Notifications\CustomerNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class PosInvoice extends Component
{
    // Cart items
    public $cart = [];

    public $cartSubtotal = 0;

    public $cartTax = 0;

    public $cartDiscount = 0;

    public $cartTotal = 0;

    // Customer info
    public $customer_name = '';

    public $customer_email = '';

    public $customer_phone = '';

    public $customer_address = '';

    // Invoice info
    public $invoice_number;

    public $notes = '';

    public $payment_method = 'cash';

    public $amount_paid = 0;

    public $change_due = 0;

    // Search
    public $searchTerm = '';

    public $searchResults = [];

    public $showResults = false;

    // Payment methods
    public $paymentMethods = [
        'cash' => 'Cash',
        'card' => 'Credit/Debit Card',
        'bank_transfer' => 'Bank Transfer',
        'mobile_money' => 'Mobile Money',
        'cheque' => 'Cheque',
        'credit' => 'Store Credit',
    ];

    // UI State
    public $showPaymentModal = false;

    public $showSuccessModal = false;

    public $showStockModal = false;

    public $createdInvoice = null;

    public $quickStockId = null;

    public $quickStockName = '';

    public $quickStockCurrentQuantity = 0;

    public $quickStockAddQuantity = 1;

    public $quickStockNotes = 'Received at POS counter.';

    // Add this property
    public $sendInvoiceEmail = true;

    protected $listeners = [
        'productSelected',
        'clearSearch',
        'confirmResendEmail', // Add this listener
    ];

    public function mount()
    {
        $this->generateInvoiceNumber();
    }

    public function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->invoice_number = 'POS-'.$year.$month.'-'.$newNumber;
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->searchResults = Stock::with(['brand', 'make'])
                ->where('quantity', '>', 0)
                ->where(function (Builder $query) {
                    $query->where('name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('sku', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('item_code', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('barcode', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('model_name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('model_number', 'like', '%'.$this->searchTerm.'%');
                })
                ->limit(10)
                ->get();
            $this->showResults = true;
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function selectProduct($productId)
    {
        $product = Stock::find($productId);

        if ($product && $product->quantity > 0) {
            // Check if product already in cart
            $existingItemKey = collect($this->cart)->search(function ($item) use ($productId) {
                return $item['stock_id'] == $productId;
            });

            if ($existingItemKey !== false) {
                // Increment quantity
                $this->cart[$existingItemKey]['quantity']++;
                $this->recalculateLineItem($existingItemKey);
            } else {
                // Add new item
                $this->cart[] = [
                    'id' => Str::random(10),
                    'stock_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => 1,
                    'unit_price' => $product->selling_price,
                    'discount' => 0,
                    'tax_rate' => 0,
                    'total' => $product->selling_price,
                    'stock_quantity' => $product->quantity,
                ];
                $this->recalculateLineItem(array_key_last($this->cart));
            }

            $this->calculateCart();
            $this->searchTerm = '';
            $this->searchResults = [];
            $this->showResults = false;

            $this->dispatch('item-added', message: 'Product added to cart');
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity > 0 && $quantity <= $this->cart[$index]['stock_quantity']) {
            $this->cart[$index]['quantity'] = $quantity;
            $this->recalculateLineItem($index);
            $this->calculateCart();
        }
    }

    public function updateDiscount($index, $discount)
    {
        if ($discount >= 0 && $discount <= 100) {
            $this->cart[$index]['discount'] = $discount;
            $this->recalculateLineItem($index);
            $this->calculateCart();
        }
    }

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateCart();
    }

    public function calculateCart()
    {
        foreach (array_keys($this->cart) as $index) {
            $this->recalculateLineItem($index);
        }

        $this->cartSubtotal = collect($this->cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $this->cartTotal = collect($this->cart)->sum('total');
        $this->cartTax = $this->cartTotal * 0; // You can implement tax logic here

        $this->calculateChange();
    }

    private function recalculateLineItem($index): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $item = $this->cart[$index];
        $baseTotal = $item['quantity'] * $item['unit_price'];
        $discountAmount = $baseTotal * (($item['discount'] ?? 0) / 100);
        $this->cart[$index]['total'] = max(0, $baseTotal - $discountAmount);
    }

    public function updatedCartDiscount()
    {
        $this->calculateCart();
    }

    public function updatedAmountPaid()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->change_due = max(0, $this->amount_paid - $this->cartTotal);
    }

    public function getCartItemsCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getCartSavingsProperty()
    {
        return max(0, $this->cartSubtotal - $this->cartTotal - $this->cartTax);
    }

    public function getPosSummaryProperty(): array
    {
        return [
            'today_sales' => Invoice::whereDate('created_at', today())->count(),
            'today_revenue' => Invoice::whereDate('created_at', today())->sum('total'),
            'today_paid' => Invoice::whereDate('created_at', today())->where('status', 'paid')->count(),
            'today_partial' => Invoice::whereDate('created_at', today())->where('balance_due', '>', 0)->count(),
        ];
    }

    public function applyQuickTender($mode)
    {
        $total = (float) $this->cartTotal;

        $this->amount_paid = match ($mode) {
            'exact' => $total,
            'plus_500' => ceil(($total + 500) / 500) * 500,
            'plus_1000' => ceil(($total + 1000) / 1000) * 1000,
            'plus_5000' => ceil(($total + 5000) / 5000) * 5000,
            default => $total,
        };

        $this->calculateChange();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->cartSubtotal = 0;
        $this->cartTax = 0;
        $this->cartTotal = 0;
        $this->amount_paid = 0;
        $this->change_due = 0;
        $this->customer_name = '';
        $this->customer_email = '';
        $this->customer_phone = '';
        $this->customer_address = '';
        $this->notes = '';
        $this->payment_method = 'cash';
        $this->generateInvoiceNumber();
    }

    public function openStockIntake(int $stockId): void
    {
        $stock = Stock::findOrFail($stockId);

        $this->quickStockId = $stock->id;
        $this->quickStockName = $stock->name;
        $this->quickStockCurrentQuantity = (int) $stock->quantity;
        $this->quickStockAddQuantity = 1;
        $this->quickStockNotes = 'Received at POS counter.';
        $this->showStockModal = true;
        $this->resetValidation([
            'quickStockAddQuantity',
            'quickStockNotes',
        ]);
    }

    public function closeStockIntake(): void
    {
        $this->showStockModal = false;
        $this->quickStockId = null;
        $this->quickStockName = '';
        $this->quickStockCurrentQuantity = 0;
        $this->quickStockAddQuantity = 1;
        $this->quickStockNotes = 'Received at POS counter.';
    }

    public function receiveStock(StockMovementService $stockMovementService): void
    {
        $this->validate([
            'quickStockId' => 'required|integer|exists:stocks,id',
            'quickStockAddQuantity' => 'required|integer|min:1|max:100000',
            'quickStockNotes' => 'nullable|string|max:255',
        ]);

        $stockMovementService->increase(
            (int) $this->quickStockId,
            (int) $this->quickStockAddQuantity,
            'pos_counter_restock',
            [
                'user_id' => auth()->id(),
                'notes' => $this->quickStockNotes ?: 'Received at POS counter.',
            ]
        );

        $updatedStock = Stock::find($this->quickStockId);

        foreach ($this->cart as $index => $item) {
            if ((int) $item['stock_id'] === (int) $this->quickStockId) {
                $this->cart[$index]['stock_quantity'] = (int) ($updatedStock?->quantity ?? $item['stock_quantity']);
            }
        }

        if (filled($this->searchTerm)) {
            $this->updatedSearchTerm();
        }

        $this->dispatch('show-success', message: 'Stock updated for '.$this->quickStockName.'.');
        $this->closeStockIntake();
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->dispatch('show-error', message: 'Cart is empty. Add items to continue.');

            return;
        }

        // Validate customer email if send invoice is checked
        if ($this->sendInvoiceEmail && empty($this->customer_email)) {
            $this->dispatch('show-error', message: 'Customer email is required to send invoice.');

            return;
        }

        $this->amount_paid = $this->cartTotal;
        $this->calculateChange();
        $this->showPaymentModal = true;
    }

    /**
     * Send invoice email with PDF attachment
     */
    private function sendInvoiceEmail($invoice, CustomerNotificationService $customerNotificationService): bool
    {
        $sent = $customerNotificationService->sendInvoice($invoice);

        if ($sent) {
            Log::info('Invoice email queued successfully to: '.$invoice->customer_email);
            $this->dispatch('show-success', message: 'Invoice email sent to '.$invoice->customer_email);
        } else {
            $this->dispatch('show-warning', message: 'Payment processed but failed to send email. Please send manually.');
        }

        return $sent;
    }

    public function processPayment(
        StockMovementService $stockMovementService,
        CustomerNotificationService $customerNotificationService,
        AuditLogService $auditLogService
    ) {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'payment_method' => 'required|string',
            'amount_paid' => 'required|numeric|min:'.($this->cartTotal * 0.5),
        ];

        // Make email required if send invoice is checked
        if ($this->sendInvoiceEmail) {
            $rules['customer_email'] = 'required|email|max:255';
        } else {
            $rules['customer_email'] = 'nullable|email|max:255';
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $this->invoice_number,
                'user_id' => auth()->id(),
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'customer_address' => $this->customer_address,
                'invoice_date' => now(),
                'subtotal' => $this->cartSubtotal,
                'tax_rate' => 0,
                'tax_amount' => $this->cartTax,
                'discount' => 0,
                'total' => $this->cartTotal,
                'amount_paid' => $this->amount_paid,
                'balance_due' => max(0, $this->cartTotal - $this->amount_paid),
                'status' => $this->amount_paid >= $this->cartTotal ? 'paid' : 'sent',
                'notes' => $this->notes,
                'payment_method' => $this->payment_method,
                'paid_at' => $this->amount_paid >= $this->cartTotal ? now() : null,
            ]);

            // Create invoice items
            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'stock_id' => $item['stock_id'],
                    'item_name' => $item['name'],
                    'item_code' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'tax_rate' => $item['tax_rate'],
                    'total' => $item['total'],
                ]);
            }

            $stockMovementService->reduceFromInvoiceItems($this->cart, 'pos_sale', [
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'user_id' => auth()->id(),
                'notes' => 'Stock reduced by POS sale.',
            ]);

            DB::commit();

            $auditLogService->log(
                'pos.invoice_created',
                $invoice,
                'POS invoice created.',
                [
                    'invoice_number' => $invoice->invoice_number,
                    'total' => $invoice->total,
                    'payment_method' => $invoice->payment_method,
                ],
                auth()->id()
            );

            // Send email if option is checked and email exists
            if ($this->sendInvoiceEmail && $this->customer_email) {
                $this->sendInvoiceEmail($invoice, $customerNotificationService);
            }

            $this->createdInvoice = $invoice;
            $this->showPaymentModal = false;
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: '.$e->getMessage());
            $this->dispatch('show-error', message: 'Error processing payment. Please try again.');
        }
    }

    /**
     * Resend invoice email manually
     */
    public function resendInvoiceEmail($invoiceId = null)
    {
        $invoiceId = $invoiceId ?? ($this->createdInvoice->id ?? null);

        if (! $invoiceId) {
            $this->dispatch('show-error', message: 'No invoice found to resend.');

            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            $this->dispatch('show-error', message: 'Invoice not found.');

            return;
        }

        if (! $invoice->customer_email) {
            $this->dispatch('show-error', message: 'No email address found for this invoice.');

            return;
        }

        $sent = $this->sendInvoiceEmail($invoice, app(CustomerNotificationService::class));

        if ($sent) {
            $this->dispatch('show-success', message: 'Invoice email resent successfully!');
        }
    }

    /**
     * Confirm and resend email from success modal
     */
    public function confirmResendEmail()
    {
        $this->resendInvoiceEmail();
    }

    public function printReceipt()
    {
        $this->dispatch('print-receipt', invoiceId: $this->createdInvoice->id);
    }

    public function newSale()
    {
        $this->clearCart();
        $this->showSuccessModal = false;
        $this->createdInvoice = null;
        $this->sendInvoiceEmail = true; // Reset the checkbox
    }

    public function render()
    {
        $billCustomizationService = app(BillCustomizationService::class);
        $billingProfiles = $billCustomizationService->configuredProfiles();
        $receiptProfile = $billCustomizationService->resolveProfile('pos_receipt', [
            'device_type' => 'desktop',
            'input_mode' => 'keyboard_scanner',
            'printer_hint' => 'Counter Thermal',
        ]);
        $printerOptions = collect($billingProfiles)
            ->pluck('printer_match')
            ->filter(fn ($printer) => filled($printer))
            ->unique()
            ->values()
            ->all();

        return view('livewire.pos-invoice', [
            'recent_invoices' => Invoice::latest()->take(5)->get(),
            'siteName' => SiteSetting::get('site_name', config('app.name', 'Display Lanka')),
            'receiptProfile' => $receiptProfile,
            'billingProfiles' => $billingProfiles,
            'printerOptions' => $printerOptions,
            'company' => $billCustomizationService->companyPayload(),
        ])->layout('layouts.admin-pos');
    }
}
