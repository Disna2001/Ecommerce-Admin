<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomatedDiscountRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'is_active'            => (bool) $this->is_active,
            'min_margin_percent'   => (float) $this->min_margin_percent,
            'max_discount_percent' => (float) $this->max_discount_percent,
            'daily_items_limit'    => (int) $this->daily_items_limit,
            'rotation_strategy'   => $this->rotation_strategy,
            'target_categories'    => $this->target_categories ?? [],
            'target_brands'        => $this->target_brands ?? [],
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
