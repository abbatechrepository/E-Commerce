<?php

namespace App\Http\Controllers\Api\Admin;

use App\Application\Catalog\PublishProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::query()->with(['artist', 'genre', 'category', 'inventory', 'primaryImage'])->paginate()
        );
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::query()->create($request->validated());
        $product->inventory()->create();

        return new ProductResource($product->load(['artist', 'genre', 'category', 'inventory']));
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['artist', 'genre', 'category', 'inventory', 'images']));
    }

    public function update(StoreProductRequest $request, Product $product): ProductResource
    {
        $this->authorize('update', $product);
        $product->update($request->validated());

        return new ProductResource($product->refresh()->load(['artist', 'genre', 'category', 'inventory']));
    }

    public function destroy(Product $product): Response
    {
        $product->update(['is_active' => false]);

        return response()->noContent();
    }

    public function publish(Product $product, PublishProductAction $publishProductAction): JsonResponse
    {
        $this->authorize('publish', $product);
        $publishedProduct = $publishProductAction->execute($product);

        return response()->json(new ProductResource($publishedProduct->load(['artist', 'genre', 'category', 'inventory'])));
    }
}
