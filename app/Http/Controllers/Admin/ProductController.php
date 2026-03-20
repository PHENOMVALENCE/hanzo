<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Factory;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'factory', 'createdByAdmin'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('factory')) {
            $query->where('factory_id', $request->factory);
        }

        $products = $query->paginate(20)->withQueryString();
        $factories = Factory::with('user')->get();

        return view('admin.products.index', compact('products', 'factories'));
    }

    public function approve(Product $product): RedirectResponse
    {
        $product->update(['status' => Product::STATUS_LIVE]);

        app(NotificationService::class)->notifyProductApproved($product);

        return back()->with('success', "Product \"{$product->title}\" approved and now live.");
    }

    public function disable(Product $product): RedirectResponse
    {
        $product->update(['status' => Product::STATUS_DISABLED]);

        return back()->with('success', "Product \"{$product->title}\" disabled.");
    }

    public function create(): View
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $factories = Factory::with('user')->get();

        return view('admin.products.create', compact('categories', 'factories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'factory_id' => ['nullable', 'integer', 'exists:factories,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,live'],
            'image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        $images = $this->processProductImages($request, null, 'products/admin');

        Product::create([
            'factory_id' => $validated['factory_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'created_by_admin_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'moq' => $validated['moq'] ?? null,
            'lead_time_days' => $validated['lead_time_days'] ?? null,
            'location' => $validated['location'] ?? null,
            'images' => $images,
            'status' => $validated['status'],
            'is_platform_product' => empty($validated['factory_id']),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $factories = Factory::with('user')->get();

        return view('admin.products.edit', compact('product', 'categories', 'factories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'factory_id' => ['nullable', 'integer', 'exists:factories,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,pending_approval,live,disabled'],
            'image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
        ]);

        $images = $this->processProductImages($request, $product, 'products/admin/' . $product->id);

        $product->update([
            'factory_id' => $validated['factory_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'moq' => $validated['moq'] ?? null,
            'lead_time_days' => $validated['lead_time_days'] ?? null,
            'location' => $validated['location'] ?? null,
            'images' => $images,
            'status' => $validated['status'],
            'is_platform_product' => empty($validated['factory_id']),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $title = $product->title;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Product \"{$title}\" deleted.");
    }

    private function processProductImages(Request $request, ?Product $product, string $storePath): array
    {
        $existing = $product ? ($product->images ?? []) : [];
        $remove = $request->input('remove_images', []);
        foreach ($remove as $path) {
            Storage::disk('public')->delete($path);
        }
        $existing = array_values(array_filter($existing, fn ($p) => ! in_array($p, $remove)));

        $newPaths = [];
        if ($request->hasFile('image')) {
            $newPaths[] = $request->file('image')->store($storePath, 'public');
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $newPaths[] = $file->store($storePath, 'public');
            }
        }

        $images = array_merge($existing, $newPaths);
        return array_slice($images, 0, 5);
    }
}
