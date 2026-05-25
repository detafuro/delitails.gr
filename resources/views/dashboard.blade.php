<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="brush-card bg-bone p-6">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
</x-app-layout>
