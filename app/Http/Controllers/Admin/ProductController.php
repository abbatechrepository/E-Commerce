<?php

namespace App\Http\Controllers\Admin;

use App\Application\Catalog\PublishProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()
                ->with(['artist', 'genre', 'category', 'inventory'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData(new Product()));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::query()->create($request->validated());
        $product->inventory()->create([
            'available_quantity' => 0,
            'reserved_quantity' => 0,
            'minimum_quantity' => 0,
        ]);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produto criado com sucesso.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', $this->formData($product->load('inventory')));
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validated();
        $inventoryData = [
            'available_quantity' => (int) $request->integer('available_quantity', $product->inventory?->available_quantity ?? 0),
            'reserved_quantity' => (int) $request->integer('reserved_quantity', $product->inventory?->reserved_quantity ?? 0),
            'minimum_quantity' => (int) $request->integer('minimum_quantity', $product->inventory?->minimum_quantity ?? 0),
        ];

        $product->update($validated);
        $product->inventory()->updateOrCreate(['product_id' => $product->id], $inventoryData);

        return back()->with('status', 'Produto atualizado com sucesso.');
    }

    public function publish(Product $product, PublishProductAction $publishProductAction): RedirectResponse
    {
        $this->authorize('publish', $product);
        $publishProductAction->execute($product);

        return back()->with('status', 'Produto publicado com sucesso.');
    }

    public function uploadImage(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        $path = $request->file('image')->store("products/{$product->id}", 'public');
        $shouldBePrimary = $request->boolean('is_primary') || ! $product->images()->where('is_primary', true)->exists();

        if ($shouldBePrimary) {
            $product->images()->update(['is_primary' => false]);
        }

        $product->images()->create([
            'image_path' => $path,
            'alt_text' => $request->string('alt_text')->toString() ?: 'Capa do album '.$product->album_title,
            'position' => ($product->images()->max('position') ?? 0) + 1,
            'is_primary' => $shouldBePrimary,
        ]);

        return back()->with('status', 'Imagem enviada com sucesso.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('status', 'Imagem principal atualizada.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        if ($image->image_path && ! str_starts_with($image->image_path, 'http')) {
            Storage::disk('public')->delete($image->image_path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->orderBy('position')->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Imagem removida com sucesso.');
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'artists' => Artist::query()->orderBy('name')->get(),
            'genres' => Genre::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
        ];
    }
}
