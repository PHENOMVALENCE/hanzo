<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SavedController extends Controller
{
    public function index()
    {
        $saved = collect(); // Placeholder – implement saved items storage
        $categories = Category::where('active', true)->get();

        return view('buyer.saved.index', compact('saved', 'categories'));
    }
}
