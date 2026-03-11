<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $currency = $request->validate([
            'currency' => ['required', 'string', 'in:USD,TZS,CNY'],
        ])['currency'];

        session(['currency' => $currency]);

        return redirect()->back();
    }
}
