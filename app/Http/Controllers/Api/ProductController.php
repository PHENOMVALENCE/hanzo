<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::forBuyerCatalog()
            ->with(['category:id,slug,name'])
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
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            );
        }

        $perPage = min((int) $request->get('limit', 12), 48);
        $products = $query->paginate($perPage);

        $data = $products->through(fn (Product $p) => [
            'id' => $p->id,
            'name' => $p->title,
            'description' => \Str::limit($p->description, 200),
            'category' => $p->category?->name,
            'category_slug' => $p->category?->slug,
            'price_display' => $p->priceDisplay(),
            'buyer_price_display' => $p->priceDisplay(),
            'moq' => $p->moq,
            'lead_time_days' => $p->lead_time_days,
            'location' => $p->location,
            'primary_image' => $p->primaryImage() ? \Storage::url($p->primaryImage()) : null,
        ]);

        return response()->json(['data' => $data->items(), 'meta' => [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]]);
    }

    public function show(Product $product): JsonResponse
    {
        if ($product->status !== Product::STATUS_LIVE) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->load('category');

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->title,
                'description' => $product->description,
                'specs' => $product->specs,
                'category' => $product->category?->name,
                'category_slug' => $product->category?->slug,
                'price_display' => $product->priceDisplay(),
                'buyer_price_display' => $product->priceDisplay(),
                'moq' => $product->moq,
                'lead_time_days' => $product->lead_time_days,
                'location' => $product->location,
                'images' => collect($product->images ?? [])->map(fn ($path) => \Storage::url($path))->all(),
            ],
        ]);
    }
}
