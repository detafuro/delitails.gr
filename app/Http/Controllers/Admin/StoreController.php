<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreStoreRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Store::class);
        $query = Store::query();
        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('postcode', 'like', "%{$search}%");
            });
        }
        $stores = $query->orderBy('sort_order')->orderBy('city')->paginate(15)->withQueryString();
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        $this->authorize('create', Store::class);
        $store = new Store();
        return view('admin.stores.create', compact('store'));
    }

    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name'].'-'.$data['city']);
        Store::create($data);
        return redirect()->route('admin.stores.index')->with('success', 'Store created.');
    }

    public function edit(Store $store)
    {
        $this->authorize('update', $store);
        return view('admin.stores.edit', compact('store'));
    }

    public function update(StoreStoreRequest $request, Store $store)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name'].'-'.$data['city']);
        $store->update($data);
        return redirect()->route('admin.stores.index')->with('success', 'Store updated.');
    }

    public function destroy(Store $store)
    {
        $this->authorize('delete', $store);
        $store->delete();
        return redirect()->route('admin.stores.index')->with('success', 'Store deleted.');
    }
}
