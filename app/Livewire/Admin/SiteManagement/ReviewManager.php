<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;

class ReviewManager extends Component
{
    use WithPagination;

    // Filters
    public string $search        = '';
    public string $filterStatus  = '';   // '' | 'approved' | 'pending' | 'flagged'
    public string $filterRating  = '';   // '' | '1'–'5'
    public string $sortField     = 'created_at';
    public string $sortDir       = 'desc';
    public int    $perPage       = 15;

    // View modal
    public ?int   $viewingId     = null;
    public bool   $showModal     = false;

    // Edit modal
    public ?int   $editingId     = null;
    public bool   $showEditModal = false;
    public int    $editRating    = 5;
    public string $editTitle     = '';
    public string $editBody      = '';
    public bool   $editApproved  = true;

    // Bulk
    public array  $selected      = [];
    public bool   $selectAll     = false;

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterRating' => ['except' => ''],
    ];

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterRating() { $this->resetPage(); }

    // ── Stats ─────────────────────────────────────────────────
    public function getStatsProperty(): array
    {
        return [
            'total'    => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending'  => Review::where('is_approved', false)->count(),
            'flagged'  => Review::where('is_flagged',  true)->count(),
            'avg'      => round(Review::where('is_approved', true)->avg('rating') ?? 0, 1),
            'five'     => Review::where('rating', 5)->count(),
            'one'      => Review::where('rating', 1)->count(),
        ];
    }

    // ── Sort ──────────────────────────────────────────────────
    public function sortBy(string $field): void
    {
        $this->sortDir   = ($this->sortField === $field && $this->sortDir === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    // ── Quick approve / reject / flag / delete ────────────────
    public function approve(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => true, 'approved_at' => now()]);
        $this->dispatch('notify', type: 'success', message: 'Review approved and published.');
    }

    public function reject(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => false, 'approved_at' => null]);
        $this->dispatch('notify', type: 'info', message: 'Review unpublished.');
    }

    public function toggleFlag(int $id): void
    {
        $review = Review::findOrFail($id);
        $review->update(['is_flagged' => !$review->is_flagged]);
        $this->dispatch('notify', type: 'info', message: $review->is_flagged ? 'Review flagged.' : 'Review unflagged.');
    }

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
        if ($this->viewingId === $id) $this->showModal = false;
        $this->dispatch('notify', type: 'error', message: 'Review deleted.');
    }

    // ── View modal ────────────────────────────────────────────
    public function viewReview(int $id): void
    {
        $this->viewingId = $id;
        $this->showModal = true;
    }

    // ── Edit modal ────────────────────────────────────────────
    public function openEdit(int $id): void
    {
        $review             = Review::findOrFail($id);
        $this->editingId    = $id;
        $this->editRating   = $review->rating;
        $this->editTitle    = $review->title ?? '';
        $this->editBody     = $review->body;
        $this->editApproved = (bool) $review->is_approved;
        $this->showEditModal = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editRating' => 'required|integer|min:1|max:5',
            'editTitle'  => 'nullable|string|max:200',
            'editBody'   => 'required|string|min:5',
        ]);

        Review::findOrFail($this->editingId)->update([
            'rating'      => $this->editRating,
            'title'       => $this->editTitle ?: null,
            'body'        => $this->editBody,
            'is_approved' => $this->editApproved,
            'approved_at' => $this->editApproved ? now() : null,
        ]);

        $this->showEditModal = false;
        $this->dispatch('notify', type: 'success', message: 'Review updated!');
    }

    // ── Bulk actions ──────────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value
            ? $this->buildQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function bulkApprove(): void
    {
        Review::whereIn('id', $this->selected)->update(['is_approved' => true, 'approved_at' => now()]);
        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'success', message: "{$count} reviews approved.");
    }

    public function bulkReject(): void
    {
        Review::whereIn('id', $this->selected)->update(['is_approved' => false]);
        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'info', message: "{$count} reviews unpublished.");
    }

    public function bulkDelete(): void
    {
        Review::whereIn('id', $this->selected)->delete();
        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'error', message: "{$count} reviews deleted.");
    }

    // ── Internal query builder ────────────────────────────────
    private function buildQuery()
    {
        return Review::with(['user', 'stock'])
            ->when($this->search, fn($q) =>
                $q->where(function($query) {
                    $query->whereHas('user',  fn($u) => $u->where('name',  'like', '%'.$this->search.'%'))
                          ->orWhereHas('stock', fn($s) => $s->where('name', 'like', '%'.$this->search.'%'))
                          ->orWhere('title', 'like', '%'.$this->search.'%')
                          ->orWhere('body',  'like', '%'.$this->search.'%');
                })
            )
            ->when($this->filterStatus === 'approved', fn($q) => $q->where('is_approved', true)->where('is_flagged', false))
            ->when($this->filterStatus === 'pending',  fn($q) => $q->where('is_approved', false))
            ->when($this->filterStatus === 'flagged',  fn($q) => $q->where('is_flagged',  true))
            ->when($this->filterRating, fn($q) => $q->where('rating', (int) $this->filterRating))
            ->orderBy($this->sortField, $this->sortDir);
    }

    // ── Render ────────────────────────────────────────────────
    public function render()
    {
        $reviews = $this->buildQuery()->paginate($this->perPage);
        
        $viewingReview = $this->viewingId
            ? Review::with(['user', 'stock', 'order'])->find($this->viewingId)
            : null;

        return view('livewire.admin.site-management.review-manager', [
            'reviews' => $reviews,
            'viewingReview' => $viewingReview,
        ]);
    }
}