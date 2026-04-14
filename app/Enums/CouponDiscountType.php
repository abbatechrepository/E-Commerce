<?php

namespace App\Enums;

enum CouponDiscountType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
