<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'artist_id',
        'genre_id',
        'category_id',
        'album_title',
        'description',
        'release_year',
        'label_name',
        'country',
        'media_format',
        'disc_condition',
        'sleeve_condition',
        'rarity_level',
        'price',
        'promotional_price',
        'cost_price',
        'weight',
        'height',
        'width',
        'length',
        'status',
        'is_active',
        'is_featured',
        'is_rare',
        'is_new_arrival',
        'is_on_sale',
        'is_best_seller',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promotional_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:3',
            'height' => 'decimal:2',
            'width' => 'decimal:2',
            'length' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_rare' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_on_sale' => 'boolean',
            'is_best_seller' => 'boolean',
            'published_at' => 'datetime',
            'status' => ProductStatus::class,
        ];
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function isPurchasable(): bool
    {
        return $this->is_active
            && $this->status?->isSellable()
            && $this->published_at !== null
            && ($this->inventory?->available_quantity ?? 0) > 0;
    }

    public function canBePublished(): bool
    {
        return filled($this->name)
            && filled($this->slug)
            && filled($this->sku)
            && filled($this->album_title)
            && filled($this->description)
            && (float) $this->price > 0
            && ($this->images()->exists() || $this->relationLoaded('images'));
    }

    public function getEffectivePriceAttribute(): string
    {
        return (string) ($this->promotional_price ?: $this->price);
    }
}
