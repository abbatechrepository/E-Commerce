<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GenreFactory extends Factory
{
    protected $model = Genre::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Jazz', 'Soul', 'MPB', 'Rock', 'Blues', 'Funk', 'Disco', 'Psychedelic Rock', 'Bossa Nova']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }
}
