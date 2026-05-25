<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreLocatorController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Store::active();
        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                  ->orWhere('postcode', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        $stores = $query->ordered()->paginate(12)->withQueryString();
        return view('site.stores', compact('stores', 'search'));
    }
}
