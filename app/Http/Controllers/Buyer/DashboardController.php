<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Category;
=======
use App\Models\Product;
>>>>>>> 3a34daee (Hanzo in b2b style)

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

<<<<<<< HEAD
        // Trending: categories with most RFQs in last 60 days
        $trending = Category::where('active', true)
            ->withCount(['rfqs as rfq_count' => fn ($q) => $q->where('created_at', '>=', now()->subDays(60))])
            ->having('rfq_count', '>', 0)
            ->orderByDesc('rfq_count')
            ->take(6)
            ->get();

        // Popular: categories with most RFQs overall (used when trending is empty)
        $popular = Category::where('active', true)
            ->withCount('rfqs')
            ->orderByDesc('rfqs_count')
            ->take(6)
            ->get();

        if ($trending->isEmpty()) {
            $trending = $popular->take(6);
        }

        // Recommended: categories not in trending, ordered by name
        $recommended = Category::where('active', true)
            ->whereNotIn('id', $trending->pluck('id'))
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('buyer.dashboard', compact(
            'showWelcomeGuide',
            'trending',
            'popular',
            'recommended'
        ));
=======
        $featuredProducts = Product::forBuyerCatalog()
            ->with('category')
            ->latest()
            ->take(15)
            ->get();

        return view('buyer.dashboard', compact('showWelcomeGuide', 'featuredProducts'));
>>>>>>> 3a34daee (Hanzo in b2b style)
    }
}
