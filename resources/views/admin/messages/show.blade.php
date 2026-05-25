<x-admin.layout title="Message" :subtitle="$message->subject ?: 'No subject'">
    <div class="brush-card p-6 max-w-3xl space-y-4">
        <div>
            <div class="text-xs uppercase tracking-wider text-ink/50">From</div>
            <div class="font-semibold">{{ $message->name }} &lt;{{ $message->email }}&gt;</div>
            @if($message->phone)<div class="text-sm text-ink/60">{{ $message->phone }}</div>@endif
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider text-ink/50">Received</div>
            <div class="text-sm">{{ $message->created_at->format('M j, Y \a\t H:i') }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider text-ink/50">Message</div>
            <div class="mt-2 whitespace-pre-line border-2 border-ink/20 bg-bone/60 p-4">{{ $message->message }}</div>
        </div>
        <div class="flex gap-3">
            <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: '.($message->subject ?? '')) }}" class="btn-rough is-fire is-sm">Reply via email</a>
            <a href="{{ route('admin.messages.index') }}" class="btn-rough is-bone is-sm">Back</a>
            <x-admin.confirm-delete :action="route('admin.messages.destroy', $message)"/>
        </div>
    </div>
</x-admin.layout>
