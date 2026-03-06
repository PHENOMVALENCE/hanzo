<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        return view('public.categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('public.categories.show', compact('category'));
    }
}
