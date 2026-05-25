<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function show()
    {
        return view('site.contact');
    }

    public function send(ContactRequest $request)
    {
        $data = $request->validated();
        unset($data['hp_field']);
        ContactMessage::create($data);
        return redirect()->route('contact')->with('success', 'Thanks for reaching out — we will be in touch soon.');
    }
}
