{{-- Navigation Bar --}}
<div class="flex items-center justify-between rounded-xl border border-white/10 bg-surface px-2 py-2 sm:px-4 sm:py-3">
    <div class="flex min-w-0 items-center gap-2 sm:gap-4">
        {{-- Back Button --}}
        <button
            wire:click="newTournament"
            class="flex shrink-0 cursor-pointer items-center gap-2 rounded-lg p-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary sm:px-3"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden text-sm font-medium sm:inline">{{ __('messages.all_tournaments') }}</span>
        </button>

        {{-- Divider --}}
        <div class="hidden h-6 w-px bg-white/10 sm:block"></div>

        {{-- Tournament Switcher --}}
        <div x-data="{ open: false }" class="relative min-w-0">
            <button
                @click="open = !open"
                class="flex cursor-pointer items-center gap-1 rounded-lg px-2 py-2 transition hover:bg-surface-light sm:gap-2 sm:px-3"
            >
                <span class="truncate text-sm font-medium sm:text-base">{{ $tournament->name }}</span>
                <svg class="h-4 w-4 shrink-0 text-text-muted transition" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown --}}
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
                class="absolute left-0 top-full z-50 mt-2 w-72 rounded-xl border border-white/10 bg-surface p-2 shadow-xl"
            >
                <div class="mb-2 px-2 text-xs font-medium uppercase tracking-wide text-text-muted">{{ __('messages.switch_tournament') }}</div>
                <div class="max-h-64 space-y-1 overflow-y-auto">
                    @foreach($this->allTournaments as $t)
                        <button
                            wire:key="switch-{{ $t->id }}"
                            wire:click="selectTournament({{ $t->id }})"
                            @click="open = false"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-left transition {{ $t->id === $tournament->id ? 'bg-primary/10 text-primary' : 'hover:bg-surface-light' }}"
                        >
                            <div>
                                <div class="font-medium {{ $t->id === $tournament->id ? '' : 'text-text-primary' }}">{{ $t->name }}</div>
                                <div class="text-xs text-text-muted">{{ localized_date($t->start_date, 'long_date') }}</div>
                            </div>
                            @if($t->id === $tournament->id)
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
                <div class="mt-2 border-t border-white/10 pt-2">
                    <button
                        wire:click="newTournament"
                        @click="open = false"
                        class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-primary transition hover:bg-primary/10"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('messages.create_new_tournament') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Menu --}}
    @auth
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex cursor-pointer items-center justify-center rounded-lg p-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>

            <div
                x-show="open"
                x-cloak
                @click.away="open = false"
                x-transition
                class="absolute right-0 top-full z-50 mt-2 w-48 rounded-xl border border-white/10 bg-surface p-2 shadow-xl"
            >
                <button
                    wire:click="startEditingTournament"
                    @click="open = false"
                    class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('messages.edit_tournament') }}
                </button>
                <button
                    wire:click="deleteTournament"
                    wire:confirm="{{ __('messages.delete_confirm') }}"
                    @click="open = false"
                    class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-danger transition hover:bg-danger/10"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('messages.delete_tournament') }}
                </button>
            </div>
        </div>
    @endauth
</div>

{{-- Tournament Info Header --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <div class="flex items-center gap-2">
            <h2 class="min-w-0 truncate text-xl font-semibold sm:text-2xl">{{ $tournament->name }}</h2>
            {{-- Manage Players button - mobile only, next to title --}}
            @auth
                <button
                    wire:click="togglePlayersDrawer"
                    class="flex shrink-0 cursor-pointer items-center rounded-lg border border-white/10 bg-surface-light p-2 text-sm font-medium transition hover:border-white/20 sm:hidden"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </button>
            @endauth
        </div>
        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-text-secondary sm:text-sm">
            <span class="flex items-center gap-1">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ localized_date_range($tournament->start_date, $tournament->end_date) }}
            </span>
            <span class="hidden h-1 w-1 rounded-full bg-text-muted sm:block"></span>
            <span>{{ $tournament->format->label() }}</span>
        </div>
    </div>

    {{-- Quick Stats + Manage Players --}}
    <div class="flex items-center gap-3 sm:gap-6">
        {{-- Players stat --}}
        <div class="flex items-center gap-1.5 sm:flex-col sm:gap-0 sm:text-center">
            <svg class="h-4 w-4 text-text-muted sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <div class="text-lg font-semibold sm:text-2xl">{{ $tournament->players->count() }}</div>
            <div class="hidden text-xs text-text-muted sm:block">{{ __('messages.players') }}</div>
        </div>
        {{-- Matches stat --}}
        <div class="flex items-center gap-1.5 sm:flex-col sm:gap-0 sm:text-center">
            <svg class="h-4 w-4 text-text-muted sm:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="9" />
                <path d="M18.5 5.5c-3 2-5 5-5 6.5s2 4.5 5 6.5" stroke-linecap="round" />
                <path d="M5.5 5.5c3 2 5 5 5 6.5s-2 4.5-5 6.5" stroke-linecap="round" />
            </svg>
            <div class="text-lg font-semibold sm:text-2xl">{{ $tournament->games->where('completed', true)->count() }}/{{ $tournament->games->count() }}</div>
            <div class="hidden text-xs text-text-muted sm:block">{{ __('messages.matches') }}</div>
        </div>
        {{-- Manage Players button - desktop only --}}
        @auth
            <button
                wire:click="togglePlayersDrawer"
                class="hidden cursor-pointer items-center gap-2 rounded-lg border border-white/10 bg-surface-light px-4 py-2 text-sm font-medium transition hover:border-white/20 sm:flex"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ __('messages.manage_players') }}
            </button>
        @endauth
    </div>
</div>
