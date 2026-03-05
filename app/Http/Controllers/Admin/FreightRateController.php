<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreightRate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FreightRateController extends Controller
{
    public function index(): View
    {
        $rates = FreightRate::latest()->paginate(20);

        return view('admin.freight-rates.index', compact('rates'));
    }

    public function create(): View
    {
        return view('admin.freight-rates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'string', 'max:50'],
            'destination_port' => ['nullable', 'string', 'max:100'],
            'destination_city' => ['nullable', 'string', 'max:100'],
            'rate_type' => ['required', 'string', 'in:per_cbm,per_kg,per_container'],
            'rate_value' => ['required', 'numeric', 'min:0'],
            'min_charge' => ['nullable', 'numeric', 'min:0'],
            'active' => ['boolean'],
        ]);

        $validated['active'] = $request->boolean('active', true);
        FreightRate::create($validated);

        return redirect()->route('admin.freight-rates.index')->with('success', 'Freight rate created.');
    }

    public function edit(FreightRate $rate): View
    {
        return view('admin.freight-rates.edit', compact('rate'));
    }

    public function update(Request $request, FreightRate $rate): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'string', 'max:50'],
            'destination_port' => ['nullable', 'string', 'max:100'],
            'destination_city' => ['nullable', 'string', 'max:100'],
            'rate_type' => ['required', 'string', 'in:per_cbm,per_kg,per_container'],
            'rate_value' => ['required', 'numeric', 'min:0'],
            'min_charge' => ['nullable', 'numeric', 'min:0'],
            'active' => ['boolean'],
        ]);

        $rate->update(array_merge($validated, ['active' => $request->boolean('active', true)]));

        return redirect()->route('admin.freight-rates.index')->with('success', 'Freight rate updated.');
    }

    public function destroy(FreightRate $rate): RedirectResponse
    {
        $rate->delete();

        return redirect()->route('admin.freight-rates.index')->with('success', 'Freight rate deleted.');
    }
}
