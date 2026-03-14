<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SupplierLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function show(): View
    {
        return view('public.partner-with-hanzo');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'product_category' => ['required', 'string', 'max:150'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        SupplierLead::create($validated);

        return redirect()->route('partner-with-hanzo')->with('success', 'Thank you for your interest. Our team will review your application and reach out if there\'s a match.');
    }
}
