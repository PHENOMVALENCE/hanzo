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

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('buyer.rfqs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'specs' => ['nullable', 'string', 'max:2000'],
            'quantity' => ['required', 'integer', 'min:1'],
            'timeline_weeks' => ['nullable', 'integer', 'min:1'],
            'delivery_country' => ['nullable', 'string', 'max:100'],
            'delivery_city' => ['nullable', 'string', 'max:100'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $files = $request->hasFile('attachments') ? $request->file('attachments') : [];
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
