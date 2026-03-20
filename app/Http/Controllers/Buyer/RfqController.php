<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Rfq;
use App\Services\RfqService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RfqController extends Controller
{
    public function __construct(
        private RfqService $rfqService
    ) {}

    public function index(): View
    {
        $rfqs = Rfq::where('buyer_id', auth()->id())
            ->with(['category', 'assignedFactory'])
            ->latest()
            ->paginate(20);

        return view('buyer.rfqs.index', compact('rfqs'));
    }

    public function create(Request $request): View
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $product = null;
        if ($request->filled('product_id')) {
            $product = \App\Models\Product::live()->find($request->product_id);
        }

        return view('buyer.rfqs.create', compact('categories', 'product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $hasProduct = $request->filled('product_id');
        $rules = [
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'timeline_weeks' => ['required', 'integer', 'in:4,6,8,12'],
            'delivery_city' => ['required', 'string', 'max:100'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpeg,jpg,png,gif,webp,pdf'],
        ];
        if ($hasProduct) {
            $rules['category_id'] = ['nullable', 'integer', 'exists:categories,id'];
            $rules['description'] = ['nullable', 'string', 'max:2000'];
            $rules['specs'] = ['nullable', 'string', 'max:2000'];
            $rules['target_price_per_unit'] = ['nullable', 'numeric', 'min:0'];
        } else {
            $rules['category_id'] = ['required', 'integer', 'exists:categories,id'];
            $rules['description'] = ['required', 'string', 'max:2000'];
            $rules['target_price_per_unit'] = ['nullable', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);
        $validated['delivery_country'] = ($validated['delivery_city'] ?? '') === 'Other' ? null : ($validated['delivery_city'] ?? null);

        if ($hasProduct && empty($validated['category_id'])) {
            $product = \App\Models\Product::find($validated['product_id']);
            $validated['category_id'] = $product?->category_id ?? Category::where('active', true)->first()?->id;
            if (empty($validated['category_id'])) {
                return back()->withErrors(['category_id' => 'No category available. Please contact support.'])->withInput();
            }
        }
        if ($hasProduct && empty($validated['description'])) {
            $product = \App\Models\Product::find($validated['product_id']);
            $validated['description'] = $product ? ($product->title . "\n\n" . ($product->description ?? '')) : '';
        }
        if ($hasProduct && !empty($validated['specs'] ?? '')) {
            $validated['description'] = ($validated['description'] ?? '') . "\n\nAdditional notes: " . $validated['specs'];
        }

        $files = $request->hasFile('attachments') ? array_slice($request->file('attachments'), 0, 5) : [];
        $rfq = $this->rfqService->create(auth()->user(), $validated, $files);

        return redirect()->route('buyer.rfqs.show', $rfq)->with('success', 'RFQ created successfully.');
    }

    public function show(Rfq $rfq): View
    {
        $this->authorize('view', $rfq);

        $rfq->load(['category', 'assignedFactory', 'attachments', 'quotations']);

        return view('buyer.rfqs.show', compact('rfq'));
    }
}
