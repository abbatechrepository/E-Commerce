<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier',
        'service_name',
        'tracking_code',
        'status',
        'shipping_cost',
        'estimated_delivery_date',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'shipping_cost' => 'decimal:2',
            'estimated_delivery_date' => 'date',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
