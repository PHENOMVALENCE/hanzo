<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $showWelcomeGuide = false;

        if ($user->first_login_at === null) {
            $user->update(['first_login_at' => now()]);
            $showWelcomeGuide = true;
        }

        $featuredProducts = Product::forBuyerCatalog()
            ->with('category')
            ->latest()
            ->take(15)
            ->get();

        return view('buyer.dashboard', compact('showWelcomeGuide', 'featuredProducts'));
    }
}
