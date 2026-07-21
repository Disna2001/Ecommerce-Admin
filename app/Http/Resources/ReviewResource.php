<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'rating'      => (int) $this->rating,
            'stars'       => $this->stars,
            'title'       => $this->title,
            'body'        => $this->body,
            'is_approved' => (bool) $this->is_approved,
            'is_flagged'  => (bool) $this->is_flagged,
            'approved_at' => $this->approved_at?->format('Y-m-d\TH:i'),
            'user'        => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'stock'       => $this->stock ? [
                'id'   => $this->stock->id,
                'name' => $this->stock->name,
                'sku'  => $this->stock->sku,
            ] : null,
            'order'       => $this->order ? [
                'id'           => $this->order->id,
                'order_number' => $this->order->order_number,
            ] : null,
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}
