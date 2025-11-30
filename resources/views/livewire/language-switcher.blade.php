<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        class="flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 bg-surface px-3 py-2 text-sm transition hover:border-white/20"
    >
        <span>{{ $languages[$currentLocale]['flag'] }}</span>
        <span class="hidden sm:inline">{{ $languages[$currentLocale]['name'] }}</span>
        <svg class="h-4 w-4 text-text-muted transition" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 top-full z-50 mt-2 w-44 rounded-xl border border-white/10 bg-surface p-2 shadow-xl"
    >
        <div class="mb-2 px-2 text-xs font-medium uppercase tracking-wide text-text-muted">{{ __('messages.language') }}</div>
        @foreach($languages as $code => $lang)
            <button
                wire:click="setLocale('{{ $code }}')"
                @click="open = false"
                class="flex w-full cursor-pointer items-center gap-3 rounded-lg px-3 py-2 text-left transition {{ $currentLocale === $code ? 'bg-primary/10 text-primary' : 'hover:bg-surface-light' }}"
            >
                <span class="text-lg">{{ $lang['flag'] }}</span>
                <span class="font-medium {{ $currentLocale === $code ? '' : 'text-text-primary' }}">{{ $lang['name'] }}</span>
                @if($currentLocale === $code)
                    <svg class="ml-auto h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>
