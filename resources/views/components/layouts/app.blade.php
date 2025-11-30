<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background font-sans text-text-primary antialiased">
    <div class="mx-auto max-w-6xl px-6 py-8">
        <header class="mb-12 flex items-start justify-between">
            <div>
                <h1 class="text-4xl font-semibold">{{ __('messages.app_name') }}</h1>
                <p class="mt-2 text-text-secondary">{{ __('messages.tournament_management') }}</p>
            </div>
            <livewire:language-switcher />
        </header>

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
