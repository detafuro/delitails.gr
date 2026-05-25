<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;

class ContactMessageController extends AdminController
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $messages = ContactMessage::latest()->paginate(20);
        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $message->update(['is_read' => true]);
        return view('admin.messages.show', compact('message'));
    }

    public function destroy(ContactMessage $message)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
