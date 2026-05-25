<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqGroup;
use Illuminate\Http\Request;

class FaqPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->string('q')->toString();

        $groups = FaqGroup::active()->ordered()
            ->with(['faqs' => fn ($q) => $q->where('is_active', true)
                ->when($search, fn ($qq) => $qq->where(function ($qqq) use ($search) {
                    $qqq->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%");
                }))
                ->orderBy('sort_order')])
            ->get()
            ->filter(fn ($g) => $g->faqs->count() > 0);

        // FAQs without a group
        $orphans = Faq::active()->whereNull('group_id')
            ->when($search, fn ($q) => $q->where(function ($qq) use ($search) {
                $qq->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%");
            }))
            ->orderBy('sort_order')->get();

        return view('site.faq', compact('groups', 'orphans', 'search'));
    }
}
