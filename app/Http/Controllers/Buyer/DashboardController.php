<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

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

        return view('buyer.dashboard', compact('showWelcomeGuide'));
    }
}
