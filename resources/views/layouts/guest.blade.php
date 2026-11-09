<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ShopFlow') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_#ccfbf1_0%,_transparent_55%),linear-gradient(180deg,#f4f6f8_0%,#eef2f7_100%)]"></div>
            <div class="pointer-events-none absolute -left-24 top-24 h-64 w-64 rounded-full bg-brand-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-16 bottom-10 h-72 w-72 rounded-full bg-emerald-200/30 blur-3xl"></div>

            <div class="relative z-10 w-full max-w-md">
                <div class="mb-8 text-center">
                    <a href="/" class="inline-flex flex-col items-center gap-3">
                        <x-application-logo class="h-14 w-14" />
                        <div>
                            <div class="text-2xl font-bold tracking-tight text-ink-heading">ShopFlow</div>
                            <div class="text-sm text-ink-muted">Inventory &amp; point of sale</div>
                        </div>
                    </a>
                </div>

                <div class="rounded-2xl border border-white/70 bg-white/90 p-6 shadow-lift backdrop-blur sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
