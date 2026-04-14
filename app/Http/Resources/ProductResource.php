<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $this->name,
            'album_title' => $this->album_title,
            'artist' => $this->artist?->name,
            'genre' => $this->genre?->name,
            'category' => $this->category?->name,
            'media_format' => $this->media_format,
            'disc_condition' => $this->disc_condition,
            'sleeve_condition' => $this->sleeve_condition,
            'rarity_level' => $this->rarity_level,
            'description' => $this->description,
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'effective_price' => $this->effective_price,
            'status' => $this->status?->value,
            'is_featured' => $this->is_featured,
            'is_rare' => $this->is_rare,
            'is_new_arrival' => $this->is_new_arrival,
            'is_on_sale' => $this->is_on_sale,
            'is_best_seller' => $this->is_best_seller,
            'inventory' => [
                'available_quantity' => $this->inventory?->available_quantity,
                'reserved_quantity' => $this->inventory?->reserved_quantity,
            ],
            'primary_image' => $this->primaryImage?->image_path,
            'published_at' => $this->published_at,
        ];
    }
}
