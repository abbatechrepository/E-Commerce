<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $album = fake()->unique()->sentence(3);

        return [
            'sku' => strtoupper(fake()->bothify('VINYL-####')),
            'name' => fake()->words(2, true),
            'slug' => Str::slug($album.'-'.fake()->unique()->numberBetween(100, 999)),
            'artist_id' => fn () => Artist::query()->inRandomOrder()->value('id') ?? Artist::factory()->create()->id,
            'genre_id' => fn () => Genre::query()->inRandomOrder()->value('id') ?? Genre::factory()->create()->id,
            'category_id' => fn () => Category::query()->inRandomOrder()->value('id') ?? Category::factory()->create()->id,
            'album_title' => $album,
            'description' => fake()->paragraphs(2, true),
            'release_year' => fake()->numberBetween(1955, 1998),
            'label_name' => fake()->company(),
            'country' => fake()->country(),
            'media_format' => fake()->randomElement(['LP', '7-inch', '12-inch']),
            'disc_condition' => fake()->randomElement(['Mint', 'Near Mint', 'Very Good Plus', 'Very Good']),
            'sleeve_condition' => fake()->randomElement(['Mint', 'Near Mint', 'Very Good Plus', 'Very Good']),
            'rarity_level' => fake()->randomElement(['common', 'collectible', 'rare']),
            'price' => fake()->randomFloat(2, 39, 420),
            'promotional_price' => fake()->boolean(25) ? fake()->randomFloat(2, 29, 250) : null,
            'cost_price' => fake()->randomFloat(2, 15, 180),
            'weight' => fake()->randomFloat(3, 0.2, 0.8),
            'height' => 31.50,
            'width' => 31.50,
            'length' => 0.70,
            'status' => ProductStatus::ACTIVE,
            'is_active' => true,
            'is_featured' => fake()->boolean(15),
            'is_rare' => fake()->boolean(20),
            'is_new_arrival' => fake()->boolean(10),
            'is_on_sale' => fake()->boolean(20),
            'is_best_seller' => fake()->boolean(10),
            'published_at' => now()->subDays(rand(1, 60)),
        ];
    }
}
