<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorefrontSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_id' => $this->page_id,
            'type' => $this->type,
            'order' => (int) $this->order,
            'is_active' => (bool) $this->is_active,
            'config' => $this->config ?? (object) [],
            'style' => $this->style ?? (object) [],
            'schema_version' => (int) $this->schema_version,
            'slot' => $this->slot ?? 'before',
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
