<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';
    case REFUNDED = 'refunded';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING_PAYMENT => in_array($target, [self::PAID, self::CANCELLED], true),
            self::PAID => in_array($target, [self::PROCESSING, self::CANCELLED, self::REFUNDED], true),
            self::PROCESSING => in_array($target, [self::SHIPPED, self::CANCELLED, self::REFUNDED], true),
            self::SHIPPED => in_array($target, [self::DELIVERED, self::RETURNED, self::REFUNDED], true),
            self::DELIVERED => in_array($target, [self::RETURNED, self::REFUNDED], true),
            self::CANCELLED, self::RETURNED, self::REFUNDED => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Aguardando pagamento',
            self::PAID => 'Pago',
            self::PROCESSING => 'Em separacao',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregue',
            self::CANCELLED => 'Cancelado',
            self::RETURNED => 'Devolvido',
            self::REFUNDED => 'Estornado',
        };
    }
}
