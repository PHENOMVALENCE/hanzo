<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::forBuyerCatalog()
            ->with(['category', 'factory'])
            ->latest();

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('min_price')) {
            $query->where('price_max', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_min', '<=', (float) $request->max_price);
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            );
        }

        $products = $query->paginate(24)->withQueryString();
        $categories = Category::where('active', true)->orderBy('name')->get();

        return view('buyer.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        if ($product->status !== Product::STATUS_LIVE) {
            abort(404);
        }

        $product->load(['category', 'factory']);

        return view('buyer.products.show', compact('product'));
    }
}
