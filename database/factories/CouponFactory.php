<?php

namespace Database\Factories;

use App\Enums\CouponDiscountType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('DISC-##??')),
            'description' => fake()->sentence(),
            'discount_type' => fake()->randomElement([CouponDiscountType::FIXED, CouponDiscountType::PERCENTAGE]),
            'discount_value' => fake()->randomElement([10, 15, 20, 30, 40]),
            'minimum_order_value' => fake()->randomElement([0, 100, 150, 200]),
            'usage_limit' => fake()->randomElement([20, 50, 100, null]),
            'usage_count' => fake()->numberBetween(0, 10),
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(45),
            'is_active' => true,
        ];
    }
}
