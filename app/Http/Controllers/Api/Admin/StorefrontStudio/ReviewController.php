<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'stock', 'order']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('stock', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if ($status === 'approved') {
            $query->where('is_approved', true)->where('is_flagged', false);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'flagged') {
            $query->where('is_flagged', true);
        }

        if ($rating = $request->input('rating')) {
            $query->where('rating', (int) $rating);
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortDir   = $request->input('sort_dir', 'desc');
        if (!in_array($sortField, ['created_at', 'rating', 'id'])) {
            $sortField = 'created_at';
        }
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 15)));
        $reviews = $query->orderBy($sortField, $sortDir)->paginate($perPage);

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending'  => Review::where('is_approved', false)->count(),
            'flagged'  => Review::where('is_flagged', true)->count(),
            'avg'      => round(Review::where('is_approved', true)->avg('rating') ?? 0, 1),
            'five'     => Review::where('rating', 5)->count(),
            'one'      => Review::where('rating', 1)->count(),
        ];

        return response()->json([
            'data'  => ReviewResource::collection($reviews),
            'meta'  => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(Review $review): ReviewResource
    {
        $review->load(['user', 'stock', 'order']);
        return new ReviewResource($review);
    }

    public function update(Request $request, Review $review): ReviewResource
    {
        $validated = $request->validate([
            'rating'      => 'required|integer|min:1|max:5',
            'title'       => 'nullable|string|max:200',
            'body'        => 'required|string|min:5',
            'is_approved' => 'boolean',
            'is_flagged'  => 'boolean',
        ]);

        $isApproved = $validated['is_approved'] ?? $review->is_approved;

        $review->update([
            'rating'      => $validated['rating'],
            'title'       => $validated['title'] ?: null,
            'body'        => $validated['body'],
            'is_approved' => $isApproved,
            'is_flagged'  => $validated['is_flagged'] ?? $review->is_flagged,
            'approved_at' => $isApproved ? ($review->approved_at ?: now()) : null,
        ]);

        $this->flushStorefrontCache();
        $review->load(['user', 'stock', 'order']);

        return new ReviewResource($review);
    }

    public function approve(Review $review): ReviewResource
    {
        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        $this->flushStorefrontCache();
        $review->load(['user', 'stock', 'order']);

        return new ReviewResource($review);
    }

    public function reject(Review $review): ReviewResource
    {
        $review->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        $this->flushStorefrontCache();
        $review->load(['user', 'stock', 'order']);

        return new ReviewResource($review);
    }

    public function toggleFlag(Review $review): ReviewResource
    {
        $review->update([
            'is_flagged' => !$review->is_flagged,
        ]);

        $review->load(['user', 'stock', 'order']);
        return new ReviewResource($review);
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();
        $this->flushStorefrontCache();

        return response()->json([
            'message' => 'Review deleted successfully.',
        ]);
    }

    public function bulk(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ids'    => 'required|array',
            'ids.*'  => 'integer|exists:reviews,id',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        if ($action === 'approve') {
            Review::whereIn('id', $ids)->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);
        } elseif ($action === 'reject') {
            Review::whereIn('id', $ids)->update([
                'is_approved' => false,
                'approved_at' => null,
            ]);
        } elseif ($action === 'delete') {
            Review::whereIn('id', $ids)->delete();
        }

        $this->flushStorefrontCache();

        return response()->json([
            'message' => "Successfully processed {$count} reviews.",
            'action'  => $action,
            'count'   => $count,
        ]);
    }

    protected function flushStorefrontCache(): void
    {
        try {
            $tenantManager = app(TenantManager::class);
            Cache::forget($tenantManager->scopedCacheKey('home_latest_reviews'));
            Cache::forget($tenantManager->scopedCacheKey('home_latest_reviews_merchant'));
            Cache::forget($tenantManager->scopedCacheKey('storefront_shared_layout_data'));
        } catch (\Throwable) {
            // Fallback
        }
    }
}
