@props(['action', 'label' => 'Delete', 'message' => 'Are you sure? This cannot be undone.'])
<div x-data="{open:false}" class="inline-block">
    <button type="button" @click="open=true" class="btn-rough is-fire is-sm">{{ $label }}</button>
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-ink/70 p-4" @click.self="open=false">
        <div class="brush-card max-w-md p-6 bg-bone">
            <h3 class="font-display text-2xl font-extrabold uppercase">Hold up</h3>
            <p class="mt-2 text-ink/70">{{ $message }}</p>
            <div class="mt-5 flex gap-3 justify-end">
                <button type="button" @click="open=false" class="btn-rough is-bone is-sm">Cancel</button>
                <form method="POST" action="{{ $action }}">
                    @csrf @method('DELETE')
                    <button class="btn-rough is-fire is-sm">Yes, delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
