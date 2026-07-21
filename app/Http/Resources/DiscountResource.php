<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'code'                 => $this->code,
            'type'                 => $this->type,
            'value'                => (float) $this->value,
            'min_order_amount'     => (float) $this->min_order_amount,
            'max_discount_amount'  => $this->max_discount_amount !== null ? (float) $this->max_discount_amount : null,
            'scope'                => $this->scope,
            'scope_id'             => $this->scope_id,
            'has_timer'            => (bool) $this->has_timer,
            'starts_at'            => $this->starts_at?->format('Y-m-d\TH:i'),
            'ends_at'              => $this->ends_at?->format('Y-m-d\TH:i'),
            'show_timer_on_site'   => (bool) $this->show_timer_on_site,
            'timer_label'          => $this->timer_label,
            'usage_limit'          => $this->usage_limit !== null ? (int) $this->usage_limit : null,
            'used_count'           => (int) ($this->used_count ?? 0),
            'is_active'            => (bool) $this->is_active,
            'is_currently_active'  => (bool) $this->isActive(),
            'is_expired'           => (bool) $this->isExpired(),
            'time_remaining_seconds' => (int) $this->timeRemainingSeconds(),
            'description'          => $this->description,
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
