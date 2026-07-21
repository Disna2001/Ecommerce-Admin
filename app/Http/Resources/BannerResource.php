<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imageUrl = null;
        if ($this->image_path) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://') || str_starts_with($this->image_path, '/storage/')) {
                $imageUrl = $this->image_path;
            } else {
                $imageUrl = Storage::url($this->image_path);
            }
        }

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'caption'     => $this->caption,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'image_path'  => $this->image_path,
            'image_url'   => $imageUrl,
            'position'    => $this->position,
            'bg_color'    => $this->bg_color,
            'text_color'  => $this->text_color,
            'is_active'   => (bool) $this->is_active,
            'sort_order'  => (int) $this->sort_order,
            'starts_at'   => $this->starts_at?->format('Y-m-d\TH:i'),
            'ends_at'     => $this->ends_at?->format('Y-m-d\TH:i'),
            'is_live'     => (bool) $this->isLive(),
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}
