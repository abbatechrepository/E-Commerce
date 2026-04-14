<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_product_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $product = Product::factory()->create([
            'artist_id' => Artist::factory(),
            'genre_id' => Genre::factory(),
            'category_id' => Category::factory(),
        ]);

        $product->inventory()->create([
            'available_quantity' => 1,
            'reserved_quantity' => 0,
            'minimum_quantity' => 0,
        ]);

        $file = UploadedFile::fake()->create('record-cover.jpg', 120, 'image/jpeg');

        $this->actingAs($admin)
            ->post(route('admin.products.images.store', $product), [
                'image' => $file,
                'alt_text' => 'Rare pressing front cover',
                'is_primary' => 1,
            ])
            ->assertRedirect();

        $product->refresh()->load('images');

        $this->assertCount(1, $product->images);
        $this->assertTrue($product->images->first()->is_primary);
        $this->assertSame('Rare pressing front cover', $product->images->first()->alt_text);
        Storage::disk('public')->assertExists($product->images->first()->image_path);
    }
}
