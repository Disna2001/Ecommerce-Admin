<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\Billing\BillCustomizationService;
use App\Services\Inventory\StockMovementService;
use App\Services\Notifications\CustomerNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Role;

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

    public $customerLookup = '';

    public $customerResults = [];

    public $showCustomerResults = false;

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

    public $input_mode = 'keyboard_scanner';

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

    public $showCustomerCreateModal = false;

    public $createdInvoice = null;

    public $heldInvoiceId = null;

    public $quickStockId = null;

    public $quickStockName = '';

    public $quickStockCurrentQuantity = 0;

    public $quickStockAddQuantity = 1;

    public $quickStockNotes = 'Received at POS counter.';

    public $quickCustomerName = '';

    public $quickCustomerEmail = '';

    public $quickCustomerPhone = '';

    public $quickCustomerAddress = '';

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
            $results = Stock::with(['brand', 'make'])
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

            $this->searchResults = $results;
            $this->showResults = true;

            if ($this->input_mode === 'keyboard_scanner') {
                $normalizedTerm = strtolower(trim($this->searchTerm));
                $exactMatches = $results->filter(function (Stock $stock) use ($normalizedTerm) {
                    return collect([
                        $stock->barcode,
                        $stock->sku,
                        $stock->item_code,
                    ])->filter()->contains(fn ($value) => strtolower(trim((string) $value)) === $normalizedTerm);
                })->values();

                if ($exactMatches->count() === 1) {
                    $this->selectProduct($exactMatches->first()->id);

                    return;
                }
            }
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function updatedCustomerLookup()
    {
        if (strlen(trim($this->customerLookup)) < 2) {
            $this->customerResults = [];
            $this->showCustomerResults = false;

            return;
        }

        $term = trim($this->customerLookup);
        $results = collect();

        Invoice::query()
            ->select(['id', 'customer_name', 'customer_email', 'customer_phone', 'customer_address'])
            ->where(function (Builder $query) use ($term) {
                $query->where('customer_name', 'like', '%'.$term.'%')
                    ->orWhere('customer_email', 'like', '%'.$term.'%')
                    ->orWhere('customer_phone', 'like', '%'.$term.'%');
            })
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Invoice $invoice) use ($results) {
                $results->push([
                    'type' => 'invoice',
                    'id' => $invoice->id,
                    'name' => $invoice->customer_name ?: 'Walk-in customer',
                    'email' => $invoice->customer_email,
                    'phone' => $invoice->customer_phone,
                    'address' => $invoice->customer_address,
                    'source' => 'Recent invoice',
                ]);
            });

        User::query()
            ->select(['id', 'name', 'email', 'phone', 'address'])
            ->where('user_type', 'regular')
            ->where(function (Builder $query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                    ->orWhere('phone', 'like', '%'.$term.'%');
            })
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (User $user) use ($results) {
                $results->push([
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'source' => 'Registered customer',
                ]);
            });

        $this->customerResults = $results
            ->unique(fn (array $result) => strtolower(trim($result['email'] ?: $result['phone'] ?: $result['name'])))
            ->take(6)
            ->values()
            ->all();

        $this->showCustomerResults = ! empty($this->customerResults);
    }

    public function selectTopSearchResult(): void
    {
        $topResult = $this->searchResults[0] ?? null;

        if ($topResult) {
            $this->selectProduct(is_array($topResult) ? $topResult['id'] : $topResult->id);
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

    public function incrementQuantity($index): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $nextQuantity = (int) $this->cart[$index]['quantity'] + 1;
        $this->updateQuantity($index, $nextQuantity);
    }

    public function decrementQuantity($index): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $nextQuantity = max(1, (int) $this->cart[$index]['quantity'] - 1);
        $this->updateQuantity($index, $nextQuantity);
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
        $this->customerLookup = '';
        $this->customerResults = [];
        $this->showCustomerResults = false;
        $this->notes = '';
        $this->payment_method = 'cash';
        $this->heldInvoiceId = null;
        $this->generateInvoiceNumber();
    }

    public function setWalkInCustomer(): void
    {
        $this->customer_name = 'Walk-in customer';

        if (blank($this->customerLookup)) {
            $this->customerLookup = $this->customer_name;
        }
    }

    public function openCustomerCreate(): void
    {
        $this->quickCustomerName = $this->customer_name && $this->customer_name !== 'Walk-in customer'
            ? $this->customer_name
            : '';
        $this->quickCustomerEmail = $this->customer_email;
        $this->quickCustomerPhone = $this->customer_phone;
        $this->quickCustomerAddress = $this->customer_address;
        $this->showCustomerCreateModal = true;
        $this->resetValidation([
            'quickCustomerName',
            'quickCustomerEmail',
            'quickCustomerPhone',
            'quickCustomerAddress',
        ]);
    }

    public function closeCustomerCreate(): void
    {
        $this->showCustomerCreateModal = false;
        $this->quickCustomerName = '';
        $this->quickCustomerEmail = '';
        $this->quickCustomerPhone = '';
        $this->quickCustomerAddress = '';
    }

    public function createQuickCustomer(): void
    {
        $this->validate([
            'quickCustomerName' => 'required|string|max:255',
            'quickCustomerEmail' => 'required|email|max:255|unique:users,email',
            'quickCustomerPhone' => 'nullable|string|max:20',
            'quickCustomerAddress' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $this->quickCustomerName,
            'email' => $this->quickCustomerEmail,
            'user_type' => 'regular',
            'password' => Hash::make(Str::random(24)),
            'phone' => $this->quickCustomerPhone ?: null,
            'address' => $this->quickCustomerAddress ?: null,
            'preferences' => [
                'created_from' => 'pos_counter',
                'requires_password_reset' => true,
            ],
            'email_verified_at' => now(),
        ]);

        $role = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->customer_name = $user->name;
        $this->customer_email = $user->email;
        $this->customer_phone = $user->phone ?: '';
        $this->customer_address = $user->address ?: '';
        $this->customerLookup = $user->name;
        $this->customerResults = [];
        $this->showCustomerResults = false;

        $this->dispatch('show-success', message: 'Customer created and loaded into the counter.');
        $this->closeCustomerCreate();
    }

    public function selectCustomerProfile(string $type, int $id): void
    {
        if ($type === 'invoice') {
            $record = Invoice::find($id, ['id', 'customer_name', 'customer_email', 'customer_phone', 'customer_address']);
        } else {
            $record = User::find($id, ['id', 'name', 'email', 'phone', 'address']);
        }

        if (! $record) {
            $this->dispatch('show-error', message: 'Customer profile could not be found.');

            return;
        }

        $this->customer_name = $type === 'invoice' ? ($record->customer_name ?: 'Walk-in customer') : $record->name;
        $this->customer_email = $type === 'invoice' ? ($record->customer_email ?: '') : ($record->email ?: '');
        $this->customer_phone = $type === 'invoice' ? ($record->customer_phone ?: '') : ($record->phone ?: '');
        $this->customer_address = $type === 'invoice' ? ($record->customer_address ?: '') : ($record->address ?: '');
        $this->customerLookup = $this->customer_name;
        $this->customerResults = [];
        $this->showCustomerResults = false;

        $this->dispatch('show-success', message: 'Customer details filled into the counter form.');
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

    public function holdSale(AuditLogService $auditLogService): void
    {
        if (empty($this->cart)) {
            $this->dispatch('show-error', message: 'Add at least one item before holding a sale.');

            return;
        }

        DB::beginTransaction();

        try {
            $invoice = $this->heldInvoiceId
                ? Invoice::with('items')->find($this->heldInvoiceId)
                : null;

            if (! $invoice) {
                $invoice = new Invoice();
                $invoice->invoice_number = $this->invoice_number;
                $invoice->user_id = auth()->id();
                $invoice->invoice_date = now();
                $invoice->status = 'draft';
            }

            $invoice->fill([
                'customer_name' => $this->customer_name ?: 'Walk-in customer',
                'customer_email' => $this->customer_email ?: null,
                'customer_phone' => $this->customer_phone ?: null,
                'customer_address' => $this->customer_address ?: null,
                'subtotal' => $this->cartSubtotal,
                'tax_rate' => 0,
                'tax_amount' => $this->cartTax,
                'discount' => 0,
                'total' => $this->cartTotal,
                'amount_paid' => 0,
                'balance_due' => $this->cartTotal,
                'status' => 'draft',
                'notes' => $this->notes,
                'payment_method' => $this->payment_method,
                'paid_at' => null,
            ]);
            $invoice->save();

            $invoice->items()->delete();

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

            DB::commit();

            $auditLogService->log(
                'pos.sale_held',
                $invoice,
                'POS sale held for later checkout.',
                [
                    'invoice_number' => $invoice->invoice_number,
                    'line_count' => count($this->cart),
                    'total' => $invoice->total,
                ],
                auth()->id()
            );

            $heldNumber = $invoice->invoice_number;
            $this->clearCart();
            $this->dispatch('show-success', message: 'Sale '.$heldNumber.' moved to held sales.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Hold sale error: '.$e->getMessage());
            $this->dispatch('show-error', message: 'Could not hold this sale right now.');
        }
    }

    public function resumeHeldSale(int $invoiceId): void
    {
        $invoice = Invoice::with('items')->where('status', 'draft')->find($invoiceId);

        if (! $invoice) {
            $this->dispatch('show-error', message: 'Held sale not found.');

            return;
        }

        $this->clearCart();
        $this->heldInvoiceId = $invoice->id;
        $this->invoice_number = $invoice->invoice_number;
        $this->customer_name = $invoice->customer_name ?: '';
        $this->customer_email = $invoice->customer_email ?: '';
        $this->customer_phone = $invoice->customer_phone ?: '';
        $this->customer_address = $invoice->customer_address ?: '';
        $this->customerLookup = $this->customer_name;
        $this->notes = $invoice->notes ?: '';
        $this->payment_method = $invoice->payment_method ?: 'cash';

        $this->cart = $invoice->items->map(function (InvoiceItem $item) {
            $stock = Stock::find($item->stock_id);

            return [
                'id' => Str::random(10),
                'stock_id' => $item->stock_id,
                'name' => $item->item_name,
                'sku' => $item->item_code,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount' => (float) $item->discount,
                'tax_rate' => (float) $item->tax_rate,
                'total' => (float) $item->total,
                'stock_quantity' => (int) ($stock?->quantity ?? $item->quantity),
            ];
        })->all();

        $this->calculateCart();
        $this->dispatch('show-success', message: 'Held sale '.$invoice->invoice_number.' restored to the counter.');
    }

    public function discardHeldSale(int $invoiceId, AuditLogService $auditLogService): void
    {
        $invoice = Invoice::with('items')->where('status', 'draft')->find($invoiceId);

        if (! $invoice) {
            $this->dispatch('show-error', message: 'Held sale not found.');

            return;
        }

        $invoiceNumber = $invoice->invoice_number;
        $invoice->items()->delete();
        $invoice->delete();

        $auditLogService->log(
            'pos.sale_discarded',
            $invoice,
            'Held POS sale discarded.',
            ['invoice_number' => $invoiceNumber],
            auth()->id()
        );

        if ($this->heldInvoiceId === $invoiceId) {
            $this->clearCart();
        }

        $this->dispatch('show-success', message: 'Held sale '.$invoiceNumber.' discarded.');
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
            $invoice = $this->heldInvoiceId
                ? Invoice::with('items')->find($this->heldInvoiceId)
                : new Invoice();

            if (! $invoice) {
                $invoice = new Invoice();
            }

            $invoice->fill([
                'invoice_number' => $invoice->exists ? $invoice->invoice_number : $this->invoice_number,
                'user_id' => auth()->id(),
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'customer_address' => $this->customer_address,
                'invoice_date' => $invoice->invoice_date ?: now(),
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
            $invoice->save();

            $invoice->items()->delete();

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
            'input_mode' => $this->input_mode,
            'printer_hint' => 'Counter Thermal',
        ]);
        $printerOptions = collect($billingProfiles)
            ->pluck('printer_match')
            ->filter(fn ($printer) => filled($printer))
            ->unique()
            ->values()
            ->all();

        return view('livewire.pos-invoice', [
            'held_sales' => Invoice::with('items')
                ->where('status', 'draft')
                ->latest('updated_at')
                ->take(6)
                ->get(),
            'recent_invoices' => Invoice::latest()->take(5)->get(),
            'siteName' => SiteSetting::get('site_name', config('app.name', 'Display Lanka')),
            'receiptProfile' => $receiptProfile,
            'billingProfiles' => $billingProfiles,
            'printerOptions' => $printerOptions,
            'company' => $billCustomizationService->companyPayload(),
        ])->layout('layouts.admin-pos');
    }
}
