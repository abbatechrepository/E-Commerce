<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with(['artist', 'genre', 'category', 'inventory', 'primaryImage'])
            ->where('is_active', true)
            ->where('status', 'active')
            ->paginate();

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['artist', 'genre', 'category', 'inventory', 'images', 'primaryImage']);

        return new ProductResource($product);
    }
}
