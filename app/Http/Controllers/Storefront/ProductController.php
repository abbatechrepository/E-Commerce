<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['artist', 'genre', 'inventory', 'primaryImage'])
            ->where('is_active', true)
            ->where('status', 'active')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('album_title', 'like', "%{$search}%");
                });
            })
            ->paginate(12)
            ->withQueryString();

        return view('storefront.products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        $product->load(['artist', 'genre', 'category', 'inventory', 'images']);

        abort_unless($product->is_active, 404);

        return view('storefront.products.show', compact('product'));
    }
}
