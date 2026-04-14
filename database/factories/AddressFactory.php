<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'label' => fake()->randomElement(['Home', 'Studio', 'Collection Room']),
            'recipient_name' => fake()->name(),
            'zip_code' => fake()->postcode(),
            'street' => fake()->streetName(),
            'number' => (string) fake()->buildingNumber(),
            'complement' => fake()->optional()->secondaryAddress(),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => 'Brazil',
            'reference' => fake()->optional()->sentence(),
            'is_default' => fake()->boolean(60),
        ];
    }
}
