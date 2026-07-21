<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Discount::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'coupon') {
                $query->whereNotNull('code')->where('code', '!=', '');
            } elseif ($type === 'auto_apply') {
                $query->where(function ($q) {
                    $q->whereNull('code')->orWhere('code', '');
                });
            }
        }

        $discounts = $query->orderByDesc('created_at')->get();

        $stats = [
            'total' => Discount::count(),
            'active' => Discount::active()->count(),
            'coupons' => Discount::whereNotNull('code')->where('code', '!=', '')->count(),
            'auto_apply' => Discount::where(function ($q) {
                $q->whereNull('code')->orWhere('code', '');
            })->count(),
            'scheduled' => Discount::whereNotNull('ends_at')->where('ends_at', '>', now())->count(),
        ];

        return response()->json([
            'data' => DiscountResource::collection($discounts),
            'stats' => $stats,
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'products'   => Stock::select('id', 'name', 'sku')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateDiscount($request);

        // Normalize empty string values
        $validated['code'] = filled($validated['code'] ?? null) ? strtoupper($validated['code']) : null;
        $validated['scope_id'] = in_array($validated['scope'], ['category', 'product']) ? ($validated['scope_id'] ?? null) : null;

        $discount = Discount::create($validated);

        return (new DiscountResource($discount))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Discount $discount): DiscountResource
    {
        return new DiscountResource($discount);
    }

    public function update(Request $request, Discount $discount): DiscountResource
    {
        $validated = $this->validateDiscount($request, $discount->id);

        $validated['code'] = filled($validated['code'] ?? null) ? strtoupper($validated['code']) : null;
        $validated['scope_id'] = in_array($validated['scope'], ['category', 'product']) ? ($validated['scope_id'] ?? null) : null;

        $discount->update($validated);

        return new DiscountResource($discount);
    }

    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json([
            'message' => 'Discount deleted successfully.',
        ]);
    }

    public function toggle(Discount $discount): DiscountResource
    {
        $discount->update([
            'is_active' => !$discount->is_active,
        ]);

        return new DiscountResource($discount);
    }

    public function generateCode(): JsonResponse
    {
        return response()->json([
            'code' => strtoupper(\Illuminate\Support\Str::random(8)),
        ]);
    }

    protected function validateDiscount(Request $request, ?int $discountId = null): array
    {
        return $request->validate([
            'name'                => 'required|string|max:200',
            'code'                => 'nullable|string|max:50|unique:discounts,code,' . ($discountId ?: 'NULL'),
            'type'                => 'required|in:percentage,fixed',
            'value'               => 'required|numeric|min:0.01',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'scope'               => 'required|in:all,category,product',
            'scope_id'            => 'nullable',
            'has_timer'           => 'boolean',
            'starts_at'           => 'nullable|date',
            'ends_at'             => 'nullable|date|after_or_equal:starts_at',
            'show_timer_on_site'  => 'boolean',
            'timer_label'         => 'nullable|string|max:100',
            'usage_limit'         => 'nullable|integer|min:1',
            'is_active'           => 'boolean',
            'description'         => 'nullable|string',
        ]);
    }
}
