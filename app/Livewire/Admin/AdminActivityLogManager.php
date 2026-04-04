<?php

namespace App\Livewire\Admin;

use App\Models\AdminActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;

class AdminActivityLogManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';
    public string $userFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;
    public bool $showDetailModal = false;
    public ?int $selectedLogId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => ''],
        'userFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'actionFilter', 'userFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function openDetailModal(int $logId): void
    {
        $this->selectedLogId = $logId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedLogId = null;
    }

    public function exportCsv()
    {
        $logs = $this->getFilteredQuery()
            ->with('user')
            ->get();

        $filename = 'admin-activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Action', 'Description', 'User', 'Subject Type', 'Subject ID', 'Properties', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->action,
                    $log->description,
                    $log->user?->name ?? 'System / Unknown',
                    $log->subject_type,
                    $log->subject_id,
                    json_encode($log->properties ?? [], JSON_UNESCAPED_SLASHES),
                    optional($log->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $logs = $this->getFilteredQuery()
            ->with('user')
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.admin-activity-logs-pdf', [
            'logs' => $logs,
            'exportedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'admin-activity-logs-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function getStatsProperty(): array
    {
        return [
            'total' => AdminActivityLog::count(),
            'today' => AdminActivityLog::whereDate('created_at', today())->count(),
            'order_actions' => AdminActivityLog::where('action', 'like', 'order.%')->count(),
            'settings_changes' => AdminActivityLog::where('action', 'like', 'settings.%')->count(),
            'invoice_actions' => AdminActivityLog::where('action', 'like', 'invoice.%')->count(),
            'pos_actions' => AdminActivityLog::where('action', 'like', 'pos.%')->count(),
        ];
    }

    protected function getFilteredQuery()
    {
        return AdminActivityLog::query()
            ->when($this->search, function ($query) {
                $query->where(function ($nested) {
                    $nested->where('action', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('subject_type', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->actionFilter, fn ($query) => $query->where('action', 'like', $this->actionFilter . '%'))
            ->when($this->userFilter, fn ($query) => $query->where('user_id', $this->userFilter))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo));
    }

    public function render()
    {
        $logs = $this->getFilteredQuery()
            ->with('user')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $users = AdminActivityLog::query()
            ->select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->with('user:id,name')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $selectedLog = $this->selectedLogId
            ? AdminActivityLog::with('user')->find($this->selectedLogId)
            : null;

        $logs->through(function ($log) {
            [$url, $label] = $this->resolveRelatedTarget($log);
            $log->related_url = $url;
            $log->related_label = $label;

            return $log;
        });

        if ($selectedLog) {
            [$url, $label] = $this->resolveRelatedTarget($selectedLog);
            $selectedLog->related_url = $url;
            $selectedLog->related_label = $label;
        }

        return view('livewire.admin.admin-activity-log-manager', [
            'logs' => $logs,
            'users' => $users,
            'stats' => $this->stats,
            'selectedLog' => $selectedLog,
        ]);
    }

    protected function resolveRelatedTarget(AdminActivityLog $log): array
    {
        return match ($log->subject_type) {
            \App\Models\Order::class => [route('admin.orders', ['focusOrder' => $log->subject_id]), 'Open Order'],
            \App\Models\Invoice::class => [route('admin.invoices', ['focusInvoice' => $log->subject_id]), 'Open Invoice'],
            \App\Models\User::class => [route('admin.users'), 'Open Users'],
            \App\Models\Stock::class => [route('admin.stocks', ['search' => $log->subject_id]), 'Open Stock'],
            default => [null, null],
        };
    }
}
