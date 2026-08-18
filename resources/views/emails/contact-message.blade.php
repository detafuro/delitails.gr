<x-mail::message>
# New message from the contact form

**From:** {{ $msg->name }} — [{{ $msg->email }}](mailto:{{ $msg->email }})@if($msg->phone) · {{ $msg->phone }}@endif

@if($msg->subject)
**Subject:** {{ $msg->subject }}
@endif

---

{{ $msg->message }}

---

<x-mail::button :url="route('admin.messages.show', $msg)">
Open in admin
</x-mail::button>

Reply to this email to answer {{ $msg->name }} directly.
</x-mail::message>
