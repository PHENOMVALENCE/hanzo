<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('factory.dashboard');
    }
}
