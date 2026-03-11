<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportDefault;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransportDefaultController extends Controller
{
    public function edit(): View
    {
        $sea = TransportDefault::getForMethod('sea') ?? $this->ensureDefault('sea');
        $air = TransportDefault::getForMethod('air') ?? $this->ensureDefault('air');

        return view('admin.transport-defaults.edit', compact('sea', 'air'));
    }

    public function update(Request $request): RedirectResponse
    {
        foreach (['sea', 'air'] as $method) {
            TransportDefault::updateOrCreate(
                ['method' => $method],
                [
                    'base_min' => (float) ($request->input("{$method}_base_min") ?? 200),
                    'per_unit_min' => (float) ($request->input("{$method}_per_unit_min") ?? 0.05),
                    'base_max' => (float) ($request->input("{$method}_base_max") ?? 800),
                    'per_unit_max' => (float) ($request->input("{$method}_per_unit_max") ?? 0.1),
                ]
            );
        }

        return redirect()->route('admin.freight-rates.index')->with('success', 'Transport defaults updated.');
    }

    protected function ensureDefault(string $method): TransportDefault
    {
        $defaults = [
            'sea' => ['base_min' => 200, 'per_unit_min' => 0.05, 'base_max' => 800, 'per_unit_max' => 0.1],
            'air' => ['base_min' => 400, 'per_unit_min' => 0.15, 'base_max' => 1500, 'per_unit_max' => 0.3],
        ];
        $d = $defaults[$method] ?? $defaults['sea'];

        return TransportDefault::firstOrCreate(
            ['method' => $method],
            $d
        );
    }
}
