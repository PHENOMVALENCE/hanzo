<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::where('active', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('slug', $request->category);
        }

        $categories = $query->orderBy('name')->get();

        return view('buyer.catalog.index', compact('categories'));
    }

    public function show(Category $category)
    {
        if (!$category->active) {
            abort(404);
        }

        return view('buyer.catalog.show', compact('category'));
    }
}
