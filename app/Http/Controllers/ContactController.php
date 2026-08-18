<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

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
        $message = ContactMessage::create($data);

        // Notify the site owner (contact_email setting, else the mail "from" address).
        $to = Setting::get('contact_email') ?: config('mail.from.address');
        if ($to) {
            try {
                Mail::to($to)->send(new ContactMessageReceived($message));
            } catch (\Throwable $e) {
                report($e); // never block the visitor because of a mail hiccup
            }
        }

        return redirect()->route('contact')->with('success', __('Thanks for reaching out — we will be in touch soon.'));
    }
}
