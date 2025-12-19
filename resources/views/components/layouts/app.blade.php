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
    {{-- Toast Notification --}}
    <div
        x-data="{
            show: false,
            pin: '',
            copied: false,
            copyPin() {
                navigator.clipboard.writeText(this.pin);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }"
        x-on:pin-generated.window="pin = $event.detail.pin; show = true; copied = false"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-4 right-4 z-50 w-80 rounded-xl border border-white/10 bg-surface p-4 shadow-2xl"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 text-sm font-medium text-success">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('messages.pin_generated') }}
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="font-mono text-2xl font-bold tracking-widest text-primary" x-text="pin"></span>
                    <button
                        @click="copyPin()"
                        class="cursor-pointer rounded-lg p-2 text-text-muted transition hover:bg-surface-light hover:text-text-secondary"
                        :title="copied ? '{{ __('messages.copied') }}' : '{{ __('messages.copy_pin') }}'"
                    >
                        <template x-if="!copied">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </template>
                        <template x-if="copied">
                            <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </button>
                </div>
            </div>
            <button
                @click="show = false"
                class="cursor-pointer rounded-lg p-1 text-text-muted transition hover:bg-surface-light hover:text-text-secondary"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

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
