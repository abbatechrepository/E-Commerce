<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'total_orders',
        'total_spent',
        'average_ticket',
        'last_order_at',
        'is_recurring',
        'favorite_genre_id',
        'favorite_artist_id',
    ];

    protected function casts(): array
    {
        return [
            'total_spent' => 'decimal:2',
            'average_ticket' => 'decimal:2',
            'last_order_at' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function favoriteGenre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'favorite_genre_id');
    }

    public function favoriteArtist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'favorite_artist_id');
    }
}
