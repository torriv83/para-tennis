<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FF793F">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/build/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon-180x180.png">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background font-sans text-text-primary antialiased">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        <header class="mb-8 sm:mb-12">
            <div class="mb-2 flex items-center justify-between gap-4">
                <h1 class="min-w-0 truncate text-2xl font-semibold sm:text-3xl md:text-4xl">{{ __('messages.app_name') }}</h1>
                <div class="flex shrink-0 items-center gap-3">
                    <livewire:admin-login />
                    <livewire:language-switcher />
                </div>
            </div>
            <p class="text-sm text-text-secondary sm:text-base">{{ __('messages.tournament_management') }}</p>
        </header>

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
