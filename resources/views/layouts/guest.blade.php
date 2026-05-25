<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased bg-bone">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0">
            <a href="/" class="flex items-center gap-2">
                <span class="font-display text-3xl font-black uppercase tracking-tight">{{ config('app.name', 'Delitails') }}</span>
            </a>

            <div class="w-full sm:max-w-md mt-8 px-6 py-6 brush-card bg-bone">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
