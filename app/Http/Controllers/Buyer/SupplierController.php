<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Services\FactoryPrivacy;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Factory::whereHas('user', fn ($q) => $q->where('status', 'approved'));

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('factory_name', 'like', "%{$q}%")
                    ->orWhere('location_china', 'like', "%{$q}%");
            });
        }

        if ($request->boolean('verified')) {
            $query->where('verification_status', 'verified');
        }

        $suppliers = $query->orderBy('factory_name')->paginate(12);
        $suppliers->getCollection()->transform(fn (Factory $f) => (object) FactoryPrivacy::forBuyer($f));

        return view('buyer.suppliers.index', compact('suppliers'));
    }

    public function show(Factory $factory)
    {
        $supplier = (object) FactoryPrivacy::forBuyer($factory);

        return view('buyer.suppliers.show', compact('supplier'));
    }
}
