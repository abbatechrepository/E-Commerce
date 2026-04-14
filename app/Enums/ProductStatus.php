<?php

namespace App\Enums;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case UNAVAILABLE = 'unavailable';

    public function isSellable(): bool
    {
        return $this === self::ACTIVE;
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Rascunho',
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::UNAVAILABLE => 'Indisponivel',
        };
    }
}
