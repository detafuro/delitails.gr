<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterRequest $request)
    {
        $email = $request->validated()['email'];
        NewsletterSubscriber::updateOrCreate(['email' => $email], ['is_active' => true]);
        return back()->with('success', 'You are signed up. Watch your inbox.');
    }
}
