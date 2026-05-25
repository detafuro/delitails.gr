<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreFaqGroupRequest;
use App\Models\FaqGroup;
use Illuminate\Support\Str;

class FaqGroupController extends AdminController
{
    public function index()
    {
        $this->authorize('viewAny', FaqGroup::class);
        $groups = FaqGroup::withCount('faqs')->orderBy('sort_order')->paginate(20);
        return view('admin.faq_groups.index', compact('groups'));
    }

    public function create()
    {
        $this->authorize('create', FaqGroup::class);
        $group = new FaqGroup();
        return view('admin.faq_groups.create', compact('group'));
    }

    public function store(StoreFaqGroupRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        FaqGroup::create($data);
        return redirect()->route('admin.faq-groups.index')->with('success', 'Group created.');
    }

    public function edit(FaqGroup $group)
    {
        $this->authorize('update', $group);
        return view('admin.faq_groups.edit', compact('group'));
    }

    public function update(StoreFaqGroupRequest $request, FaqGroup $group)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $group->update($data);
        return redirect()->route('admin.faq-groups.index')->with('success', 'Group updated.');
    }

    public function destroy(FaqGroup $group)
    {
        $this->authorize('delete', $group);
        $group->delete();
        return redirect()->route('admin.faq-groups.index')->with('success', 'Group deleted.');
    }
}
