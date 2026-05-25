<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriberController extends AdminController
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $subscribers = NewsletterSubscriber::latest()->paginate(30);
        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }
}
