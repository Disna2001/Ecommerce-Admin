<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\AutomatedDiscountRuleResource;
use App\Models\AutomatedDiscountRule;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DailyDiscount;
use App\Services\Promotions\DiscountOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutomatedDiscountController extends Controller
{
    public function show(): JsonResponse
    {
        $rule = AutomatedDiscountRule::first();

        if (!$rule) {
            $rule = AutomatedDiscountRule::create([
                'is_active' => true,
                'min_margin_percent' => 10,
                'max_discount_percent' => 30,
                'daily_items_limit' => 10,
                'rotation_strategy' => 'random',
                'target_categories' => [],
                'target_brands' => [],
            ]);
        }

        $todayDate = now()->toDateString();
        $dailyDiscounts = DailyDiscount::with('stock')
            ->where('applied_date', $todayDate)
            ->latest()
            ->get();

        $dailyStats = [
            'active_today'  => $dailyDiscounts->count(),
            'total_value'   => (float) $dailyDiscounts->sum('original_price'),
            'savings_value' => (float) $dailyDiscounts->sum(DB::raw('original_price - discounted_price')),
            'avg_discount'  => (float) ($dailyDiscounts->avg('discount_percent') ?? 0),
        ];

        return response()->json([
            'rule'           => new AutomatedDiscountRuleResource($rule),
            'daily_discounts' => $dailyDiscounts,
            'daily_stats'    => $dailyStats,
            'categories'     => Category::select('id', 'name')->orderBy('name')->get(),
            'brands'         => Brand::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active'            => 'boolean',
            'min_margin_percent'   => 'required|numeric|min:0|max:100',
            'max_discount_percent' => 'required|numeric|min:0|max:100',
            'daily_items_limit'    => 'required|integer|min:1|max:100',
            'rotation_strategy'   => 'required|in:random,slow_moving,overstocked',
            'target_categories'    => 'nullable|array',
            'target_categories.*'  => 'integer|exists:categories,id',
            'target_brands'        => 'nullable|array',
            'target_brands.*'      => 'integer|exists:brands,id',
        ]);

        $rule = AutomatedDiscountRule::first();

        if ($rule) {
            $rule->update($validated);
        } else {
            $rule = AutomatedDiscountRule::create($validated);
        }

        return response()->json([
            'message' => 'Automated discount rules updated successfully.',
            'rule'    => new AutomatedDiscountRuleResource($rule),
        ]);
    }

    public function orchestrate(DiscountOrchestrator $orchestrator): JsonResponse
    {
        try {
            $count = $orchestrator->generateDailyDiscounts();
            return response()->json([
                'message' => $count > 0
                    ? "Successfully synchronized {$count} items to today's discount registry."
                    : "Orchestration complete. No qualifying items found based on active rules.",
                'applied_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Orchestration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
