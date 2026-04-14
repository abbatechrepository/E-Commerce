<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovado',
            self::DECLINED => 'Recusado',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Estornado',
            self::FAILED => 'Falhou',
        };
    }
}
