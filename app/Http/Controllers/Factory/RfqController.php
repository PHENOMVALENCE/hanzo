<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\FactoryQuote;
use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RfqController extends Controller
{
    public function index(): View
    {
        $factory = auth()->user()->factory;
        if (! $factory) {
            return view('factory.rfqs.index', ['rfqs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20)]);
        }
        $rfqs = Rfq::where('assigned_factory_id', $factory->id)
            ->with(['buyer', 'category'])
            ->latest()
            ->paginate(20);

        return view('factory.rfqs.index', compact('rfqs'));
    }

    public function show(Rfq $rfq): View
    {
        $this->authorize('view', $rfq);

        $rfq->load(['category', 'attachments', 'factoryQuotes']);

        return view('factory.rfqs.show', compact('rfq'));
    }

    public function submitPrice(Request $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('view', $rfq);

        $factory = auth()->user()->factory;

        $validated = $request->validate([
            'unit_price_usd' => ['required', 'numeric', 'min:0'],
            'moq_confirmed' => ['nullable', 'integer', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'notes_internal' => ['nullable', 'string', 'max:1000'],
        ]);

        FactoryQuote::create([
            'rfq_id' => $rfq->id,
            'factory_id' => $factory->id,
            'unit_price_usd' => $validated['unit_price_usd'],
            'moq_confirmed' => $validated['moq_confirmed'] ?? null,
            'lead_time_days' => $validated['lead_time_days'] ?? null,
            'notes_internal' => $validated['notes_internal'] ?? null,
            'status' => 'submitted',
        ]);

        $rfq->update(['status' => 'pricing_received']);

        return back()->with('success', 'Price submitted successfully.');
    }
}
