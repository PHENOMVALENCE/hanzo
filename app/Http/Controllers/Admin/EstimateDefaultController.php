<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstimateDefault;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstimateDefaultController extends Controller
{
    public function edit(): View
    {
        $estimate = EstimateDefault::current() ?? EstimateDefault::create([
            'factory_min' => 2.50,
            'factory_max' => 25.00,
            'freight_min' => 1.00,
            'freight_max' => 500.00,
            'customs_min' => 200.00,
            'customs_max' => 3750.00,
            'total_min' => 2701.00,
            'total_max' => 29250.00,
            'moq' => 500,
            'currency' => 'usd',
            'active' => true,
        ]);

        return view('admin.estimate-defaults.edit', compact('estimate'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'factory_min' => ['nullable', 'numeric', 'min:0'],
            'factory_max' => ['nullable', 'numeric', 'min:0'],
            'freight_min' => ['nullable', 'numeric', 'min:0'],
            'freight_max' => ['nullable', 'numeric', 'min:0'],
            'customs_min' => ['nullable', 'numeric', 'min:0'],
            'customs_max' => ['nullable', 'numeric', 'min:0'],
            'total_min' => ['nullable', 'numeric', 'min:0'],
            'total_max' => ['nullable', 'numeric', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:10'],
            'active' => ['boolean'],
        ]);

        $estimate = EstimateDefault::current() ?? new EstimateDefault();
        $estimate->fill($validated);
        $estimate->active = $request->boolean('active', true);
        $estimate->save();

        return redirect()->route('admin.estimate-defaults.edit')
            ->with('success', 'Estimate defaults updated.');
    }
}

