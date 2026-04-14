<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PENDING = 'pending';
    case READY_FOR_DISPATCH = 'ready_for_dispatch';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::READY_FOR_DISPATCH => 'Pronto para envio',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregue',
            self::RETURNED => 'Devolvido',
            self::CANCELLED => 'Cancelado',
        };
    }
}
