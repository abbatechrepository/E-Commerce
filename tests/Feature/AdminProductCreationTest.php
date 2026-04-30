<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
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

class AdminProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_inventory_from_form(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'sku' => 'LP-NEW-001',
                'name' => 'Clube da Esquina Original',
                'slug' => 'clube-da-esquina-original',
                'artist_id' => $artist->id,
                'genre_id' => $genre->id,
                'category_id' => $category->id,
                'album_title' => 'Clube da Esquina',
                'description' => 'Primeira prensagem em excelente estado.',
                'release_year' => 1972,
                'label_name' => 'EMI Odeon',
                'country' => 'Brasil',
                'media_format' => 'LP',
                'disc_condition' => 'VG+',
                'sleeve_condition' => 'VG+',
                'rarity_level' => 'Raro',
                'price' => 299.90,
                'promotional_price' => 279.90,
                'cost_price' => 180.00,
                'available_quantity' => 3,
                'reserved_quantity' => 1,
                'minimum_quantity' => 1,
                'status' => ProductStatus::DRAFT->value,
                'is_active' => '1',
                'is_featured' => '1',
            ])
            ->assertRedirect();

        $product = Product::query()
            ->where('sku', 'LP-NEW-001')
            ->with('inventory')
            ->first();

        $this->assertNotNull($product);
        $this->assertSame('Clube da Esquina', $product->album_title);
        $this->assertSame('3', (string) $product->inventory?->available_quantity);
        $this->assertSame('1', (string) $product->inventory?->reserved_quantity);
        $this->assertSame('1', (string) $product->inventory?->minimum_quantity);
    }

    public function test_admin_can_create_product_with_cover_image_from_form(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();
        $category = Category::factory()->create();
        $cover = UploadedFile::fake()->create('cover.jpg', 120, 'image/jpeg');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'sku' => 'LP-NEW-002',
                'name' => 'Acabou Chorare 1972',
                'slug' => 'acabou-chorare-1972',
                'artist_id' => $artist->id,
                'genre_id' => $genre->id,
                'category_id' => $category->id,
                'album_title' => 'Acabou Chorare',
                'description' => 'Edicao com capa original e encarte.',
                'release_year' => 1972,
                'label_name' => 'Som Livre',
                'country' => 'Brasil',
                'media_format' => 'LP',
                'disc_condition' => 'VG',
                'sleeve_condition' => 'VG+',
                'rarity_level' => 'Colecionador',
                'price' => 249.90,
                'available_quantity' => 2,
                'reserved_quantity' => 0,
                'minimum_quantity' => 1,
                'status' => ProductStatus::DRAFT->value,
                'is_active' => '1',
                'cover_image' => $cover,
                'alt_text' => 'Capa do album Acabou Chorare',
                'is_primary' => '1',
            ])
            ->assertRedirect();

        $product = Product::query()
            ->where('sku', 'LP-NEW-002')
            ->with('images')
            ->first();

        $this->assertNotNull($product);
        $this->assertCount(1, $product->images);
        $this->assertTrue($product->images->first()->is_primary);
        $this->assertSame('Capa do album Acabou Chorare', $product->images->first()->alt_text);
        Storage::disk('public')->assertExists($product->images->first()->image_path);
    }
}
