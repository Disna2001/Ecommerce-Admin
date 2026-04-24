<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\Billing\BillCustomizationService;
use App\Services\Inventory\StockMovementService;
use App\Services\Notifications\CustomerNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceManager extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $dateFrom;

    public $dateTo;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 10;

    public ?int $viewingInvoiceId = null;

    public bool $showDetailModal = false;

    public ?string $focusInvoice = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'focusInvoice' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if ($this->focusInvoice && ctype_digit((string) $this->focusInvoice)) {
            $this->openInvoice((int) $this->focusInvoice);
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function getAttentionQueuesProperty(): array
    {
        return [
            [
                'label' => 'Overdue Invoices',
                'count' => Invoice::where('status', 'overdue')->count(),
                'description' => 'Customer balances that need follow-up.',
                'icon' => 'fa-clock',
                'tone' => 'rose',
                'action' => 'focusOverdue',
            ],
            [
                'label' => 'Pending Payment',
                'count' => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
                'description' => 'Invoices still waiting for settlement.',
                'icon' => 'fa-money-bill-wave',
                'tone' => 'amber',
                'action' => 'focusPending',
            ],
            [
                'label' => 'Email Not Sent',
                'count' => Invoice::whereNotNull('customer_email')->whereNull('email_sent_at')->count(),
                'description' => 'Invoices that can still be mailed to customers.',
                'icon' => 'fa-envelope',
                'tone' => 'sky',
                'action' => 'focusUnsent',
            ],
            [
                'label' => 'Partial Balance',
                'count' => Invoice::where('balance_due', '>', 0)->where('amount_paid', '>', 0)->count(),
                'description' => 'Partially collected sales needing closure.',
                'icon' => 'fa-scale-balanced',
                'tone' => 'violet',
                'action' => 'focusPartial',
            ],
        ];
    }

    public function focusOverdue(): void
    {
        $this->statusFilter = 'overdue';
        $this->resetPage();
    }

    public function focusPending(): void
    {
        $this->statusFilter = 'sent';
        $this->resetPage();
    }

    public function focusUnsent(): void
    {
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function focusPartial(): void
    {
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Invoice::with(['user', 'supplier', 'items'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_name', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_email', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_phone', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom, function (Builder $query) {
                $query->whereDate('invoice_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->whereDate('invoice_date', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $invoices = $query->paginate($this->perPage);

        $stats = [
            'total_invoices' => Invoice::count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('balance_due'),
            'overdue_count' => Invoice::where('status', 'overdue')->count(),
            'paid_count' => Invoice::where('status', 'paid')->count(),
            'unsent_emails' => Invoice::whereNotNull('customer_email')->whereNull('email_sent_at')->count(),
            'partial_count' => Invoice::where('balance_due', '>', 0)->where('amount_paid', '>', 0)->count(),
        ];

        return view('livewire.invoice-manager', [
            'invoices' => $invoices,
            'stats' => $stats,
            'recentInvoices' => Invoice::latest()->take(5)->get(),
            'viewingInvoice' => $this->viewingInvoiceId
                ? Invoice::with(['user', 'supplier', 'items.stock'])->find($this->viewingInvoiceId)
                : null,
        ])->layout('layouts.admin');
    }

    public function openInvoice(int $id): void
    {
        $this->viewingInvoiceId = $id;
        $this->showDetailModal = true;
        $this->focusInvoice = (string) $id;
    }

    public function closeInvoice(): void
    {
        $this->showDetailModal = false;
        $this->focusInvoice = null;
    }

    public function delete($id, StockMovementService $stockMovementService, AuditLogService $auditLogService)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        if ($invoice->status === 'sent' || $invoice->status === 'paid') {
            $stockMovementService->restoreFromInvoice($invoice, 'invoice_deleted', [
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'user_id' => auth()->id(),
                'notes' => 'Stock restored after invoice deletion.',
            ]);
        }

        $invoice->delete();
        $auditLogService->log('invoice.deleted', $invoice, 'Invoice deleted and stock restored when required.', [
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
        ], auth()->id());
        session()->flash('message', 'Invoice deleted successfully.');
    }

    public function markAsPaid($id, AuditLogService $auditLogService)
    {
        $invoice = Invoice::find($id);
        $invoice->status = 'paid';
        $invoice->amount_paid = $invoice->total;
        $invoice->balance_due = 0;
        $invoice->paid_at = now();
        $invoice->save();
        $auditLogService->log('invoice.marked_paid', $invoice, 'Invoice marked as paid.', [
            'invoice_number' => $invoice->invoice_number,
            'total' => $invoice->total,
        ], auth()->id());

        session()->flash('message', 'Invoice marked as paid.');
    }

    public function markAsCancelled($id, StockMovementService $stockMovementService, AuditLogService $auditLogService)
    {
        $invoice = Invoice::with('items')->find($id);

        if ($invoice->status === 'sent') {
            $stockMovementService->restoreFromInvoice($invoice, 'invoice_cancelled', [
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'user_id' => auth()->id(),
                'notes' => 'Stock restored after invoice cancellation.',
            ]);
        }

        $invoice->status = 'cancelled';
        $invoice->save();
        $auditLogService->log('invoice.cancelled', $invoice, 'Invoice cancelled and stock restored when required.', [
            'invoice_number' => $invoice->invoice_number,
        ], auth()->id());

        session()->flash('message', 'Invoice cancelled.');
    }

    public function resendInvoiceEmail($id, CustomerNotificationService $customerNotificationService)
    {
        $invoice = Invoice::findOrFail($id);

        if (! $invoice->customer_email) {
            session()->flash('message', 'This invoice does not have a customer email address.');

            return;
        }

        $sent = $customerNotificationService->sendInvoice($invoice);
        session()->flash('message', $sent ? 'Invoice email sent successfully.' : 'Failed to send invoice email.');
    }

    public function downloadPdf($id, BillCustomizationService $billCustomizationService)
    {
        $invoice = Invoice::with(['items', 'user', 'supplier'])->findOrFail($id);
        $data = $billCustomizationService->invoiceViewData($invoice, [
            'device_type' => 'desktop',
            'input_mode' => 'manual',
            'printer_hint' => 'Office A4',
        ]);
        $billProfile = $data['billProfile'];
        $pdf = Pdf::loadView('exports.invoice-pdf', $data)
            ->setPaper(
                $billCustomizationService->paperConfig($billProfile),
                $billCustomizationService->paperOrientation($billProfile)
            );

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'invoice-'.$invoice->invoice_number.'.pdf'
        );
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
}
