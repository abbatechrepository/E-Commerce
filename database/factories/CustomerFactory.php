<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'marketing_consent' => fake()->boolean(70),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
