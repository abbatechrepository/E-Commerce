<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case INITIAL = 'initial';
    case RESTOCK = 'restock';
    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
    case RESERVATION = 'reservation';
    case RELEASE = 'release';
    case SALE = 'sale';
    case RETURN = 'return';
}
