<?php

namespace App\Services\Promotions;

use App\Models\AutomatedDiscountRule;
use App\Models\DailyDiscount;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountOrchestrator
{
    /**
     * Orchestrate daily discounts based on active rules.
     */
    public function generateDailyDiscounts($date = null): int
    {
        $date = $date ?: Carbon::today()->toDateString();
        
        $rule = AutomatedDiscountRule::where('is_active', true)->first();
        if (!$rule) {
            Log::info('No active automated discount rule found for ' . $date);
            return 0;
        }

        // 1. Identify candidates
        $query = Stock::where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereNotNull('unit_price')
            ->whereNotNull('selling_price')
            ->where('selling_price', '>', 0);

        if ($rule->target_categories) {
            $query->whereIn('category_id', $rule->target_categories);
        }

        if ($rule->target_brands) {
            $query->whereIn('brand_id', $rule->target_brands);
        }

        // 2. Select items based on strategy
        $candidates = $this->selectCandidates($query, $rule);

        $appliedCount = 0;

        DB::transaction(function () use ($candidates, $rule, $date, &$appliedCount) {
            // Clear existing for this date if any (re-generation)
            DailyDiscount::where('applied_date', $date)->delete();

            foreach ($candidates as $stock) {
                $discountData = $this->calculateSafeDiscount($stock, $rule);
                
                if ($discountData) {
                    DailyDiscount::create([
                        'tenant_id' => $stock->tenant_id,
                        'stock_id' => $stock->id,
                        'discount_percent' => $discountData['percent'],
                        'original_price' => $stock->selling_price,
                        'discounted_price' => $discountData['discounted_price'],
                        'applied_date' => $date,
                    ]);
                    $appliedCount++;
                }
            }
        });

        return $appliedCount;
    }

    /**
     * Select items based on rotation strategy.
     */
    protected function selectCandidates($query, $rule)
    {
        return match ($rule->rotation_strategy) {
            'slow_moving' => $query->orderBy('updated_at', 'asc')->limit($rule->daily_items_limit)->get(),
            'overstocked' => $query->orderBy('quantity', 'desc')->limit($rule->daily_items_limit)->get(),
            default => $query->inRandomOrder()->limit($rule->daily_items_limit)->get(),
        };
    }

    /**
     * Calculate a safe discount percentage and price.
     */
    protected function calculateSafeDiscount(Stock $stock, AutomatedDiscountRule $rule): ?array
    {
        $cost = (float) $stock->unit_price;
        $sellingPrice = (float) $stock->selling_price;
        
        // Ensure we don't start with zero cost
        if ($cost <= 0) return null;

        // Calculate maximum possible discount while maintaining min margin
        // formula: SellingPrice - DiscountValue >= Cost + (Cost * MinMargin%)
        $minProfit = $cost * ($rule->min_margin_percent / 100);
        $minPriceAllowed = $cost + $minProfit;
        
        $maxDiscountValue = $sellingPrice - $minPriceAllowed;
        
        if ($maxDiscountValue <= 0) return null;

        $maxDiscountPercent = ($maxDiscountValue / $sellingPrice) * 100;
        
        // Cap by rule's max discount percent
        $targetPercent = min($maxDiscountPercent, (float) $rule->max_discount_percent);
        
        // Add some randomization for "different discount on each items"
        // We go between 5% and the target percent
        if ($targetPercent < 5) return null;
        
        $finalPercent = rand(5, (int) floor($targetPercent));
        
        $discountValue = round($sellingPrice * ($finalPercent / 100), 2);
        $discountedPrice = $sellingPrice - $discountValue;

        return [
            'percent' => $finalPercent,
            'discounted_price' => $discountedPrice,
        ];
    }
}
