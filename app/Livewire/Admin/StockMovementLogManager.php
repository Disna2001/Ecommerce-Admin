<?php

namespace App\Livewire\Admin;

use App\Models\StockMovementLog;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementLogManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $directionFilter = '';
    public string $contextFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 15;
    public bool $showDetailModal = false;
    public ?int $selectedMovementId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'directionFilter' => ['except' => ''],
        'contextFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'directionFilter', 'contextFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedMovementId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedMovementId = null;
        $this->showDetailModal = false;
    }

    public function getStatsProperty(): array
    {
        return [
            'total' => StockMovementLog::count(),
            'out' => StockMovementLog::where('direction', 'out')->count(),
            'in' => StockMovementLog::where('direction', 'in')->count(),
            'checkout' => StockMovementLog::where('context', 'order_checkout')->count(),
            'pos' => StockMovementLog::where('context', 'pos_sale')->count(),
            'restored' => StockMovementLog::whereIn('context', ['invoice_cancelled', 'invoice_deleted', 'order_cancelled', 'order_refunded'])->count(),
        ];
    }

    protected function getFilteredQuery()
    {
        return StockMovementLog::query()
            ->with(['stock', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($nested) {
                    $nested->where('context', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%')
                        ->orWhereHas('stock', fn ($stock) => $stock->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->directionFilter, fn ($query) => $query->where('direction', $this->directionFilter))
            ->when($this->contextFilter, fn ($query) => $query->where('context', $this->contextFilter))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo));
    }

    public function render()
    {
        $movements = $this->getFilteredQuery()
            ->latest()
            ->paginate($this->perPage);

        $selectedMovement = $this->selectedMovementId
            ? StockMovementLog::with(['stock', 'user'])->find($this->selectedMovementId)
            : null;

        $contexts = StockMovementLog::query()->select('context')->distinct()->orderBy('context')->pluck('context');

        return view('livewire.admin.stock-movement-log-manager', [
            'movements' => $movements,
            'selectedMovement' => $selectedMovement,
            'contexts' => $contexts,
            'stats' => $this->stats,
        ]);
    }
}
