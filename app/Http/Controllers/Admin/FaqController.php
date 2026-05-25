<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreFaqRequest;
use App\Models\Faq;
use App\Models\FaqGroup;
use Illuminate\Http\Request;

class FaqController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Faq::class);
        $query = Faq::query()->with('group');
        if ($search = $request->string('q')->toString()) {
            $query->where('question', 'like', "%{$search}%");
        }
        if ($groupId = $request->integer('group_id')) {
            $query->where('group_id', $groupId);
        }
        $faqs = $query->orderBy('sort_order')->paginate(15)->withQueryString();
        $groups = FaqGroup::ordered()->get();
        return view('admin.faqs.index', compact('faqs', 'groups'));
    }

    public function create()
    {
        $this->authorize('create', Faq::class);
        $faq = new Faq();
        $groups = FaqGroup::ordered()->get();
        return view('admin.faqs.create', compact('faq', 'groups'));
    }

    public function store(StoreFaqRequest $request)
    {
        Faq::create($request->validated());
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq)
    {
        $this->authorize('update', $faq);
        $groups = FaqGroup::ordered()->get();
        return view('admin.faqs.edit', compact('faq', 'groups'));
    }

    public function update(StoreFaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $this->authorize('delete', $faq);
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }
}
