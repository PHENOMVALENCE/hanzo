<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->validate([
            'locale' => ['required', 'string', 'in:en,sw'],
        ])['locale'];

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
