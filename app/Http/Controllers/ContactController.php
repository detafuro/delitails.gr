<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Support\Turnstile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function show()
    {
        return view('site.contact');
    }

    public function send(ContactRequest $request)
    {
        if (! Turnstile::verify($request->input('cf-turnstile-response'), $request->ip())) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => __('Could not verify you are human. Please try again.'),
            ]);
        }

        $data = $request->validated();
        unset($data['hp_field']);

        // People click Send several times when nothing seems to happen; treat an
        // identical message from the same address within 10 minutes as the same one.
        $existing = ContactMessage::where('email', $data['email'])
            ->where('message', $data['message'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($existing) {
            return redirect()->route('contact')->with('success', __('Thanks for reaching out — we will be in touch soon.'));
        }

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
