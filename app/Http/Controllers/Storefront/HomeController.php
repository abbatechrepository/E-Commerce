<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('storefront.home', [
            'featuredProducts' => Product::query()
                ->with(['artist', 'genre', 'inventory', 'primaryImage'])
                ->where('is_active', true)
                ->where('status', 'active')
                ->orderByRaw('COALESCE(published_at, created_at) DESC')
                ->take(8)
                ->get(),
        ]);
    }
}
