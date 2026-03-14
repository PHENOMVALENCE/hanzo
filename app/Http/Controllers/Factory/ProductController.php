<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $factory = auth()->user()->factory;
        if (!$factory) {
            return redirect()->route('factory.dashboard')
                ->with('error', 'No factory profile. Please contact HANZO Admin.');
        }

        $products = Product::forFactory($factory->id)
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('factory.products.index', compact('products'));
    }

    public function create()
    {
        $factory = auth()->user()->factory;
        if (!$factory) {
            return redirect()->route('factory.dashboard')
                ->with('error', 'No factory profile.');
        }

        $categories = Category::where('active', true)->orderBy('name')->get();

        return view('factory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $factory = auth()->user()->factory;
        if (!$factory) {
            return redirect()->route('factory.dashboard')->with('error', 'No factory profile.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'moq' => ['required', 'integer', 'min:1'],
            'price_per_unit' => ['nullable', 'numeric', 'min:0'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'lead_time_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['factory_id'] = $factory->id;
        $validated['status'] = Product::STATUS_DRAFT;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $validated['sku'] = Product::makeSku($validated['name']);

        Product::create($validated);

        return redirect()->route('factory.products.index')
            ->with('success', 'Product posted successfully.');
    }

    public function edit(Product $product)
    {
        $factory = auth()->user()->factory;
        if (!$factory || $product->factory_id !== $factory->id) {
            abort(404);
        }

        $categories = Category::where('active', true)->orderBy('name')->get();

        return view('factory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $factory = auth()->user()->factory;
        if (!$factory || $product->factory_id !== $factory->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'moq' => ['required', 'integer', 'min:1'],
            'price_per_unit' => ['nullable', 'numeric', 'min:0'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'lead_time_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('factory.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $factory = auth()->user()->factory;
        if (!$factory || $product->factory_id !== $factory->id) {
            abort(404);
        }

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('factory.products.index')
            ->with('success', 'Product removed.');
    }
}
