<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    private function getFactory()
    {
        $factory = auth()->user()->factory;
        if (! $factory) {
            abort(403, 'No factory linked to your account.');
        }
        return $factory;
    }

    public function index(): View
    {
        $factory = $this->getFactory();
        $products = Product::where('factory_id', $factory->id)
            ->with('category')
            ->latest()
            ->paginate(20);

        return view('factory.products.index', compact('products'));
    }

    public function create(): View
    {
        $this->getFactory();
        $categories = Category::where('active', true)->orderBy('name')->get();

        return view('factory.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $factory = $this->getFactory();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price_min' => ['required', 'numeric', 'min:0'],
            'price_max' => ['required', 'numeric', 'min:0', 'gte:price_min'],
            'moq' => ['required', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,pending_approval'],
            'image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        $images = $this->processProductImages($request, null, 'products/' . $factory->id);

        $product = Product::create([
            'factory_id' => $factory->id,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'moq' => $validated['moq'] ?? null,
            'lead_time_days' => $validated['lead_time_days'] ?? null,
            'location' => $validated['location'] ?? $factory->location_china,
            'images' => $images,
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'pending_approval') {
            app(NotificationService::class)->notifyProductSubmitted($product);
        }

        return redirect()->route('factory.products.index')->with('success', 'Product created. ' . ($validated['status'] === 'pending_approval' ? 'Awaiting admin approval.' : 'Saved as draft.'));
    }

    public function edit(Product $product): View
    {
        $factory = $this->getFactory();
        if ($product->factory_id !== $factory->id) {
            abort(403);
        }
        $categories = Category::where('active', true)->orderBy('name')->get();

        return view('factory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $factory = $this->getFactory();
        if ($product->factory_id !== $factory->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
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

        $images = $this->processProductImages($request, $product, 'products/' . $factory->id);

        // Factory cannot set live; only admin can approve. If current is live and they choose live, keep it.
        $newStatus = $validated['status'];
        if ($newStatus === 'live' && $product->status !== Product::STATUS_LIVE) {
            $newStatus = $product->status; // revert to current
        }

        $wasPendingApproval = $product->status === Product::STATUS_PENDING_APPROVAL;

        $product->update([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
            'moq' => $validated['moq'] ?? null,
            'lead_time_days' => $validated['lead_time_days'] ?? null,
            'location' => $validated['location'] ?? null,
            'images' => $images,
            'status' => $newStatus,
        ]);

        if ($newStatus === Product::STATUS_PENDING_APPROVAL && ! $wasPendingApproval) {
            app(NotificationService::class)->notifyProductSubmitted($product->fresh());
        }

        return redirect()->route('factory.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $factory = $this->getFactory();
        if ($product->factory_id !== $factory->id) {
            abort(403);
        }
        $product->update(['status' => Product::STATUS_DISABLED]);

        return redirect()->route('factory.products.index')->with('success', 'Product archived.');
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
