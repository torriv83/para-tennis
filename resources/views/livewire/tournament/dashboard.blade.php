<div class="space-y-8">
    @if(!$tournament)
        {{-- Hero Section --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-surface via-surface to-surface-light p-8 md:p-12">
            {{-- Decorative elements --}}
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-secondary/5 blur-3xl"></div>

            <div class="relative">
                <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-sm text-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    {{ __('messages.new_tournament') }}
                </div>

                <h2 class="mb-3 text-3xl font-semibold md:text-4xl">{{ __('messages.create_your_tournament') }}</h2>
                <p class="mb-8 max-w-xl text-text-secondary">
                    {{ __('messages.create_tournament_description') }}
                </p>

                <form wire:submit="createTournament" class="space-y-6">
                    {{-- Tournament Name --}}
                    <div>
                        <label for="tournamentName" class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_name') }}</label>
                        <input
                            type="text"
                            id="tournamentName"
                            wire:model="tournamentName"
                            class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-lg text-text-primary placeholder-text-muted backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            placeholder="{{ __('messages.tournament_name_placeholder') }}"
                        >
                        @error('tournamentName') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_dates') }}</label>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input
                                    type="date"
                                    wire:model="startDate"
                                    class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>
                            <span class="text-text-muted">{{ __('messages.to') }}</span>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    wire:model="endDate"
                                    class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>
                        </div>
                        @error('startDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                        @error('endDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- Format Selection --}}
                    <div>
                        <label class="mb-3 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_format') }}</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="group cursor-pointer">
                                <input type="radio" wire:model="tournamentFormat" value="round_robin" class="peer hidden">
                                <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ __('messages.round_robin') }}</span>
                                    </div>
                                    <p class="text-sm text-text-muted">{{ __('messages.round_robin_desc') }}</p>
                                </div>
                            </label>

                            <label class="group cursor-pointer">
                                <input type="radio" wire:model="tournamentFormat" value="round_robin_finals" class="peer hidden">
                                <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                            <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ __('messages.round_robin_finals') }}</span>
                                    </div>
                                    <p class="text-sm text-text-muted">{{ __('messages.round_robin_finals_desc') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('messages.create_tournament') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Tournament History --}}
        @if($this->allTournaments->count() > 0)
            <div class="rounded-xl border border-white/10 bg-surface p-6">
                <h3 class="mb-4 text-lg font-medium">{{ __('messages.previous_tournaments') }}</h3>
                <div class="space-y-2">
                    @foreach($this->allTournaments as $t)
                        <button
                            wire:key="history-{{ $t->id }}"
                            wire:click="selectTournament({{ $t->id }})"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg border border-white/5 bg-surface-light px-4 py-3 text-left transition hover:border-white/20"
                        >
                            <div>
                                <span class="font-medium">{{ $t->name }}</span>
                                <span class="ml-2 text-sm text-text-muted">
                                    {{ localized_date_range($t->start_date, $t->end_date) }}
                                </span>
                            </div>
                            <span class="text-sm text-text-muted">
                                {{ $t->players->count() }} {{ __('messages.players') }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        {{-- Navigation Bar --}}
        <div class="flex items-center justify-between rounded-xl border border-white/10 bg-surface px-4 py-3">
            <div class="flex items-center gap-4">
                {{-- Back Button --}}
                <button
                    wire:click="newTournament"
                    class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-sm font-medium">{{ __('messages.all_tournaments') }}</span>
                </button>

                {{-- Divider --}}
                <div class="h-6 w-px bg-white/10"></div>

                {{-- Tournament Switcher --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-surface-light"
                    >
                        <span class="font-medium">{{ $tournament->name }}</span>
                        <svg class="h-4 w-4 text-text-muted transition" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        </div>

        {{-- Tournament Info Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold">{{ $tournament->name }}</h2>
                <div class="mt-1 flex items-center gap-3 text-sm text-text-secondary">
                    <span class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ localized_date_range($tournament->start_date, $tournament->end_date) }}
                    </span>
                    <span class="h-1 w-1 rounded-full bg-text-muted"></span>
                    <span>{{ $tournament->format->label() }}</span>
                </div>
            </div>

            {{-- Quick Stats + Manage Players --}}
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <div class="text-2xl font-semibold">{{ $tournament->players->count() }}</div>
                    <div class="text-xs text-text-muted">{{ __('messages.players') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-semibold">{{ $tournament->games->where('completed', true)->count() }}/{{ $tournament->games->count() }}</div>
                    <div class="text-xs text-text-muted">{{ __('messages.matches') }}</div>
                </div>
                <button
                    wire:click="togglePlayersDrawer"
                    class="flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 bg-surface-light px-4 py-2 text-sm font-medium transition hover:border-white/20"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('messages.manage_players') }}
                </button>
            </div>
        </div>

        {{-- Tab Bar --}}
        <div class="flex gap-1 rounded-lg border border-white/10 bg-surface p-1">
            <button
                wire:click="$set('activeTab', 'overview')"
                class="flex-1 cursor-pointer rounded-md px-4 py-2 text-sm font-medium transition {{ $activeTab === 'overview' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
            >
                {{ __('messages.overview') }}
            </button>
            <button
                wire:click="$set('activeTab', 'matches')"
                class="flex-1 cursor-pointer rounded-md px-4 py-2 text-sm font-medium transition {{ $activeTab === 'matches' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
            >
                {{ __('messages.matches') }}
            </button>
        </div>

        {{-- Tab Content --}}
        @if($activeTab === 'overview')
            {{-- Overview Tab: Standings + Predictions side by side, Next Up below --}}

            {{-- Next Up Widget --}}
            @if($this->nextUp['type'] !== 'none')
                <div class="rounded-xl border border-primary/30 bg-gradient-to-r from-primary/10 to-transparent p-4">
                    <div class="mb-3 flex items-center gap-2">
                        @if($this->nextUp['type'] === 'today')
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                        <h3 class="font-medium {{ $this->nextUp['type'] === 'today' ? 'text-primary' : 'text-secondary' }}">{{ $this->nextUp['type'] === 'today' ? __('messages.playing_today') : __('messages.next_match') }}</h3>
                    </div>
                    <div class="space-y-2">
                        @foreach($this->nextUp['games'] as $game)
                            <div wire:key="nextup-{{ $game->id }}" class="flex items-center justify-between rounded-lg bg-surface/50 p-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium">{{ $game->player1->name }}</span>
                                    <span class="text-text-muted">{{ __('messages.vs') }}</span>
                                    <span class="font-medium">{{ $game->player2->name }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-text-muted">{{ localized_date($game->scheduled_at, 'time') }}</span>
                                    <button
                                        wire:click="$set('activeTab', 'matches')"
                                        class="cursor-pointer rounded-md bg-primary px-3 py-1 text-sm font-medium text-white transition hover:bg-primary-hover"
                                    >
                                        {{ __('messages.enter_result') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- Standings Section --}}
                <div class="rounded-xl border border-white/10 bg-surface p-6">
                    <h3 class="mb-4 text-lg font-medium">{{ __('messages.standings') }}</h3>

                    @if(count($this->standings) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-text-muted">
                                        <th class="pb-3 pr-4">#</th>
                                        <th class="pb-3 pr-4">{{ __('messages.player') }}</th>
                                        <th class="cursor-help pb-3 pr-4 text-center" title="{{ __('messages.played_full') }}">{{ __('messages.played_abbr') }}</th>
                                        <th class="cursor-help pb-3 pr-4 text-center" title="{{ __('messages.wins_full') }}">{{ __('messages.wins_abbr') }}</th>
                                        <th class="cursor-help pb-3 pr-4 text-center" title="{{ __('messages.losses_full') }}">{{ __('messages.losses_abbr') }}</th>
                                        <th class="cursor-help pb-3 pr-4 text-center" title="{{ __('messages.points_full') }}">{{ __('messages.points_abbr') }}</th>
                                        <th class="cursor-help pb-3 pr-4 text-center" title="{{ __('messages.sets_full') }}">{{ __('messages.sets_abbr') }}</th>
                                        <th class="cursor-help pb-3 text-center" title="{{ __('messages.games_full') }}">{{ __('messages.games_abbr') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->standings as $index => $standing)
                                        <tr wire:key="standing-{{ $standing['player']->id }}" class="border-b border-white/5">
                                            <td class="py-3 pr-4 font-medium text-text-secondary">{{ $index + 1 }}</td>
                                            <td class="py-3 pr-4 font-medium">{{ $standing['player']->name }}</td>
                                            <td class="py-3 pr-4 text-center text-text-secondary">{{ $standing['played'] }}</td>
                                            <td class="py-3 pr-4 text-center text-success">{{ $standing['wins'] }}</td>
                                            <td class="py-3 pr-4 text-center text-danger">{{ $standing['losses'] }}</td>
                                            <td class="py-3 pr-4 text-center font-semibold text-amber-400">{{ $standing['wins'] }}</td>
                                            @php
                                                $setDiff = $standing['sets_won'] - $standing['sets_lost'];
                                                $gameDiff = $standing['games_won'] - $standing['games_lost'];
                                            @endphp
                                            <td class="py-3 pr-4 text-center text-text-secondary">
                                                {{ $standing['sets_won'] }}-{{ $standing['sets_lost'] }}
                                                <span class="ml-1 text-xs {{ $setDiff > 0 ? 'text-success' : ($setDiff < 0 ? 'text-danger' : 'text-text-muted') }}">
                                                    ({{ $setDiff > 0 ? '+' : '' }}{{ $setDiff }})
                                                </span>
                                            </td>
                                            <td class="py-3 text-center text-text-secondary">
                                                {{ $standing['games_won'] }}-{{ $standing['games_lost'] }}
                                                <span class="ml-1 text-xs {{ $gameDiff > 0 ? 'text-success' : ($gameDiff < 0 ? 'text-danger' : 'text-text-muted') }}">
                                                    ({{ $gameDiff > 0 ? '+' : '' }}{{ $gameDiff }})
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-text-muted">{{ __('messages.no_matches_completed') }}</p>
                    @endif
                </div>

                {{-- Predictions Section --}}
                <div class="rounded-xl border border-white/10 bg-surface p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <h3 class="text-lg font-medium">{{ __('messages.predictions') }}</h3>
                    </div>

                    @if(count($this->predictions) > 0)
                        <div class="space-y-3">
                            @php
                                // Sort predictions by best_position (ascending), then worst_position as tiebreaker
                                $sortedPredictions = collect($this->predictions)
                                    ->map(fn($pred, $playerId) => array_merge($pred, ['player_id' => $playerId]))
                                    ->sortBy([
                                        ['best_position', 'asc'],
                                        ['worst_position', 'asc'],
                                    ])
                                    ->values();
                            @endphp
                            @foreach($sortedPredictions as $prediction)
                                @php
                                    $player = $tournament->players->firstWhere('id', $prediction['player_id']);
                                @endphp
                                @if($player)
                                    <div wire:key="prediction-{{ $player->id }}" class="rounded-lg border border-white/5 bg-surface-light p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="mb-1 flex items-center gap-2">
                                                    <span class="font-medium">{{ $player->name }}</span>
                                                    @if($prediction['clinched'])
                                                        <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-medium text-success">
                                                            {{ __('messages.clinched') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-text-secondary">{{ $prediction['summary'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                @if($prediction['best_position'] === $prediction['worst_position'])
                                                    <span class="text-2xl font-bold text-primary">{{ $prediction['best_position'] }}</span>
                                                @else
                                                    <span class="text-lg text-text-muted">
                                                        {{ $prediction['best_position'] }}-{{ $prediction['worst_position'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if(count($prediction['scenarios']) > 1)
                                            <div x-data="{ open: false }" class="mt-3 border-t border-white/5 pt-3">
                                                <button
                                                    @click="open = !open"
                                                    class="cursor-pointer text-xs text-text-muted hover:text-text-secondary"
                                                >
                                                    <span x-text="open ? '{{ __('messages.hide_scenarios') }}' : '{{ __('messages.show_scenarios') }}'">{{ __('messages.show_scenarios') }}</span>
                                                </button>
                                                <div x-show="open" x-cloak class="mt-2 space-y-1">
                                                    @foreach($prediction['scenarios'] as $scenario)
                                                        <div class="flex items-start gap-2 text-sm">
                                                            <span class="shrink-0 rounded bg-surface px-1.5 py-0.5 text-xs font-medium {{ $scenario['position'] <= 2 ? 'text-success' : 'text-text-muted' }}">
                                                                {{ $scenario['position_label'] }}
                                                            </span>
                                                            <span class="text-text-secondary">
                                                                {{ $scenario['conditions'] }}
                                                                @if(!empty($scenario['external_conditions']))
                                                                    <span class="text-text-muted">({{ __('messages.if') }} {{ $scenario['external_conditions'] }})</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-text-muted">{{ __('messages.predictions_available_after') }}</p>
                    @endif
                </div>
            </div>

            {{-- Round Robin Champion Section (only for round_robin format when all games complete) --}}
            @if($tournament->format === \App\TournamentFormat::RoundRobin && $this->tournamentChampion)
                <div class="rounded-xl border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-transparent p-6 text-center">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-4 py-1.5 text-amber-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.tournament_champion') }}
                    </div>
                    <div class="text-3xl font-bold text-amber-400">{{ $this->tournamentChampion->name }}</div>
                    <div class="mt-2 text-text-muted">
                        {{ __('messages.round_robin_winner') }}
                    </div>
                </div>
            @endif

            {{-- Finals Section (only for round_robin_finals format) --}}
            @if($tournament->format === \App\TournamentFormat::RoundRobinFinals)
                <div class="rounded-xl border border-primary/30 bg-gradient-to-br from-primary/10 via-surface to-surface p-6">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/20">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ __('messages.championship_final') }}</h3>
                            <p class="text-sm text-text-muted">{{ __('messages.top_2_compete') }}</p>
                        </div>
                    </div>

                    @if($this->tournamentChampion)
                        {{-- Champion Display --}}
                        <div class="rounded-xl border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-transparent p-6 text-center">
                            <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-4 py-1.5 text-amber-400">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.tournament_champion') }}
                            </div>
                            <div class="text-3xl font-bold text-amber-400">{{ $this->tournamentChampion->name }}</div>
                            @if($this->finalMatch)
                                <div class="mt-2 text-text-muted">
                                    @if($this->finalMatch->is_walkover)
                                        {{ __('messages.won_by_walkover') }}
                                    @else
                                        @php
                                            $isChampionPlayer1 = $this->finalMatch->player1_id === $this->tournamentChampion->id;
                                            $opponentName = $isChampionPlayer1 ? $this->finalMatch->player2->name : $this->finalMatch->player1->name;
                                            $winnerSets = $isChampionPlayer1 ? $this->finalMatch->player1_sets : $this->finalMatch->player2_sets;
                                            $loserSets = $isChampionPlayer1 ? $this->finalMatch->player2_sets : $this->finalMatch->player1_sets;
                                        @endphp
                                        {{ __('messages.defeated') }} {{ $opponentName }} {{ $winnerSets }}-{{ $loserSets }}
                                        @if($this->finalMatch->set_scores)
                                            @php
                                                $setScoresDisplay = $isChampionPlayer1
                                                    ? collect($this->finalMatch->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ')
                                                    : collect($this->finalMatch->set_scores)->map(fn($s) => $s[1].'-'.$s[0])->join(', ');
                                            @endphp
                                            ({{ $setScoresDisplay }})
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    @elseif($this->finalMatch)
                        {{-- Final Match Card --}}
                        @php $final = $this->finalMatch; @endphp
                        <div class="rounded-lg border border-white/10 bg-surface-light p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-1 items-center gap-3">
                                    <div class="text-center">
                                        <div class="text-xs text-text-muted">#1</div>
                                        <div class="font-semibold {{ $final->completed && $final->player1_sets > $final->player2_sets ? 'text-success' : '' }}">
                                            {{ $final->player1->name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3" x-data="{
                                    set1P1: 0, set1P2: 0,
                                    set2P1: 0, set2P2: 0,
                                    set3P1: 0, set3P2: 0,
                                    showSet3: false,
                                    editing: false,
                                    get set1Winner() {
                                        const p1 = this.set1P1, p2 = this.set1P2;
                                        if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
                                        if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
                                        return 0;
                                    },
                                    get set2Winner() {
                                        const p1 = this.set2P1, p2 = this.set2P2;
                                        if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
                                        if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
                                        return 0;
                                    },
                                    get needsSet3() {
                                        return this.set1Winner !== 0 && this.set2Winner !== 0 && this.set1Winner !== this.set2Winner;
                                    },
                                    get p1Sets() {
                                        let sets = 0;
                                        if (this.set1Winner === 1) sets++;
                                        if (this.set2Winner === 1) sets++;
                                        if (this.showSet3 && this.set3P1 > this.set3P2) sets++;
                                        return sets;
                                    },
                                    get p2Sets() {
                                        let sets = 0;
                                        if (this.set1Winner === 2) sets++;
                                        if (this.set2Winner === 2) sets++;
                                        if (this.showSet3 && this.set3P2 > this.set3P1) sets++;
                                        return sets;
                                    },
                                    get p1Games() {
                                        return this.set1P1 + this.set2P1 + (this.showSet3 ? this.set3P1 : 0);
                                    },
                                    get p2Games() {
                                        return this.set1P2 + this.set2P2 + (this.showSet3 ? this.set3P2 : 0);
                                    },
                                    get canSave() {
                                        return this.p1Sets === 2 || this.p2Sets === 2;
                                    }
                                }" x-effect="showSet3 = needsSet3">
                                    @if($final->completed)
                                        @if($final->is_walkover)
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full bg-amber-500/20 px-3 py-1 font-semibold text-amber-400">W.O.</span>
                                                <span class="text-sm text-text-muted">{{ $final->walkoverWinner?->name }} wins</span>
                                            </div>
                                        @else
                                            <div x-show="!editing" class="flex items-center gap-2">
                                                <span class="rounded-full bg-secondary/20 px-4 py-2 text-xl font-bold text-secondary">
                                                    {{ $final->player1_sets }} - {{ $final->player2_sets }}
                                                </span>
                                                <span class="text-sm text-text-muted">
                                                    @if($final->set_scores)
                                                        ({{ collect($final->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ') }})
                                                    @else
                                                        ({{ $final->player1_games }}-{{ $final->player2_games }})
                                                    @endif
                                                </span>
                                                <button @click="editing = true" class="ml-2 cursor-pointer text-text-muted transition hover:text-primary" title="Edit result">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @else
                                        <div x-show="!editing" class="flex items-center gap-2">
                                            <button @click="editing = true" class="cursor-pointer rounded-md border border-secondary/50 bg-secondary/10 px-4 py-2 text-sm font-medium text-secondary transition hover:bg-secondary/20">
                                                {{ __('messages.enter_final_result') }}
                                            </button>
                                            <div class="relative" x-data="{ showWalkover: false }">
                                                <button @click="showWalkover = !showWalkover" class="cursor-pointer rounded-md border border-amber-500/30 px-3 py-2 text-sm text-amber-400 transition hover:border-amber-400 hover:bg-amber-500/10" title="Record walkover">
                                                    W.O.
                                                </button>
                                                <div x-show="showWalkover" x-cloak @click.away="showWalkover = false" class="absolute right-0 top-full z-10 mt-2 w-48 rounded-lg border border-white/10 bg-surface p-2 shadow-xl">
                                                    <p class="mb-2 px-2 text-xs text-text-muted">{{ __('messages.select_winner') }}</p>
                                                    <button wire:click="recordWalkover({{ $final->id }}, {{ $final->player1_id }})" @click="showWalkover = false" class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light">
                                                        {{ $final->player1->name }}
                                                    </button>
                                                    <button wire:click="recordWalkover({{ $final->id }}, {{ $final->player2_id }})" @click="showWalkover = false" class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light">
                                                        {{ $final->player2->name }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Score Entry Form --}}
                                    <div x-show="editing" x-cloak class="flex flex-col gap-3" @keydown.enter="if (canSave) { $wire.updateGameResult({{ $final->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }">
                                        <div class="flex items-center gap-4">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xs text-text-muted">{{ __('messages.set') }} 1</span>
                                                <div class="flex items-center gap-1">
                                                    <input type="number" x-model.number="set1P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                                    <span class="text-text-muted">-</span>
                                                    <input type="number" x-model.number="set1P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xs text-text-muted">{{ __('messages.set') }} 2</span>
                                                <div class="flex items-center gap-1">
                                                    <input type="number" x-model.number="set2P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                                    <span class="text-text-muted">-</span>
                                                    <input type="number" x-model.number="set2P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                                </div>
                                            </div>
                                            <div x-show="showSet3" class="flex flex-col items-center gap-1">
                                                <span class="text-xs text-secondary">{{ __('messages.set') }} 3</span>
                                                <div class="flex items-center gap-1">
                                                    <input type="number" x-model.number="set3P1" min="0" class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm">
                                                    <span class="text-text-muted">-</span>
                                                    <input type="number" x-model.number="set3P2" min="0" class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm">
                                                </div>
                                            </div>
                                            <button x-show="!showSet3" @click="showSet3 = true" class="cursor-pointer rounded border border-dashed border-white/20 px-2 py-1 text-xs text-text-muted transition hover:border-secondary hover:text-secondary">
                                                + {{ __('messages.set') }} 3
                                            </button>
                                        </div>
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-text-muted">
                                                    {{ __('messages.result') }}: <span class="font-medium text-text-primary" x-text="p1Sets + ' - ' + p2Sets"></span>
                                                    (<span x-text="p1Games + '-' + p2Games"></span>)
                                                </span>
                                                <button
                                                    @click="if (canSave) { $wire.updateGameResult({{ $final->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }"
                                                    :disabled="!canSave"
                                                    :class="canSave ? 'cursor-pointer bg-secondary hover:bg-secondary/90' : 'cursor-not-allowed bg-gray-500 opacity-50'"
                                                    class="rounded px-3 py-1 text-sm font-medium text-white"
                                                >
                                                    {{ __('messages.save') }}
                                                </button>
                                                <button @click="editing = false; showSet3 = false; set1P1 = 0; set1P2 = 0; set2P1 = 0; set2P2 = 0; set3P1 = 0; set3P2 = 0;" class="cursor-pointer text-text-muted hover:text-text-secondary">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <span x-show="needsSet3 && (set3P1 === set3P2)" x-cloak class="text-xs text-amber-400">
                                                {{ __('messages.set3_required') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-1 items-center justify-end gap-3">
                                    <div class="text-center">
                                        <div class="text-xs text-text-muted">#2</div>
                                        <div class="font-semibold {{ $final->completed && $final->player2_sets > $final->player1_sets ? 'text-success' : '' }}">
                                            {{ $final->player2->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($this->roundRobinComplete)
                        {{-- This shouldn't happen - final should auto-create, but just in case --}}
                        <div class="rounded-lg border border-dashed border-secondary/50 bg-secondary/5 p-4 text-center">
                            <p class="text-text-muted">{{ __('messages.round_robin_complete_notice') }}</p>
                        </div>
                    @else
                        {{-- Round Robin not complete yet --}}
                        <div class="rounded-lg border border-dashed border-white/20 bg-surface/50 p-4 text-center">
                            <p class="text-text-muted">{{ __('messages.complete_round_robin') }}</p>
                            @php
                                $roundRobinGames = $tournament->games->where('is_final', false);
                                $completedCount = $roundRobinGames->where('completed', true)->count();
                                $totalCount = $roundRobinGames->count();
                            @endphp
                            @if($totalCount > 0)
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <div class="h-2 w-32 overflow-hidden rounded-full bg-surface-light">
                                        <div class="h-full bg-secondary transition-all" style="width: {{ ($completedCount / $totalCount) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm text-text-muted">{{ $completedCount }}/{{ $totalCount }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- Generate Schedule CTA (if no games yet) --}}
            @if($tournament->games->isEmpty() && $tournament->players->count() >= 2)
                <div class="rounded-xl border border-dashed border-primary/50 bg-primary/5 p-6 text-center">
                    <p class="mb-4 text-text-secondary">{{ __('messages.ready_to_start') }}</p>
                    <button
                        wire:click="generateSchedule"
                        class="cursor-pointer rounded-lg bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover"
                    >
                        {{ __('messages.generate_match_schedule') }}
                    </button>
                </div>
            @elseif($tournament->players->count() < 2)
                <div class="rounded-xl border border-dashed border-white/20 bg-surface p-6 text-center">
                    <p class="text-text-muted">{{ __('messages.add_players_first') }}</p>
                    <button
                        wire:click="togglePlayersDrawer"
                        class="mt-4 cursor-pointer text-primary hover:underline"
                    >
                        Manage Players
                    </button>
                </div>
            @endif

        @elseif($activeTab === 'matches')
            {{-- Matches Tab: Full match list --}}
            @if($tournament->games->count() > 0)
                <div class="rounded-xl border border-white/10 bg-surface p-6">
                    <h3 class="mb-4 text-lg font-medium">{{ __('messages.all_matches') }}</h3>

                    <div class="space-y-3">
                        @foreach($tournament->games->where('is_final', false)->sortBy('scheduled_at') as $game)
                            <div wire:key="game-{{ $game->id }}-{{ $game->scheduled_at?->timestamp ?? 'none' }}-{{ $game->completed ? 'done' : 'pending' }}" class="rounded-lg border border-white/5 bg-surface-light p-4">
                                {{-- Schedule Row --}}
                                <div class="mb-3 flex items-center justify-between border-b border-white/5 pb-3" x-data="{
                                    scheduledAt: '{{ $game->scheduled_at?->format('Y-m-d\TH:i') ?? '' }}',
                                    editingSchedule: false
                                }">
                                    <div class="flex items-center gap-2 text-sm text-text-muted">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <template x-if="!editingSchedule">
                                            <span
                                                @click="editingSchedule = true"
                                                class="cursor-pointer hover:text-text-secondary"
                                            >
                                                @if($game->scheduled_at)
                                                    {{ localized_date($game->scheduled_at, 'datetime') }}
                                                @else
                                                    {{ __('messages.set_schedule') }}
                                                @endif
                                            </span>
                                        </template>
                                        <template x-if="editingSchedule">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="datetime-local"
                                                    x-model="scheduledAt"
                                                    min="{{ $tournament->start_date->format('Y-m-d\TH:i') }}"
                                                    max="{{ $tournament->end_date?->endOfDay()->format('Y-m-d\TH:i') ?? $tournament->start_date->endOfDay()->format('Y-m-d\TH:i') }}"
                                                    step="900"
                                                    @keydown.enter="$wire.updateGameSchedule({{ $game->id }}, scheduledAt); editingSchedule = false"
                                                    class="cursor-pointer rounded border border-white/20 bg-background px-2 py-1 text-sm"
                                                >
                                                <button
                                                    @click="$wire.updateGameSchedule({{ $game->id }}, scheduledAt); editingSchedule = false"
                                                    class="cursor-pointer rounded bg-primary px-2 py-1 text-xs font-medium text-white"
                                                >
                                                    Save
                                                </button>
                                                <button
                                                    @click="editingSchedule = false"
                                                    class="cursor-pointer text-text-muted hover:text-text-secondary"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    @if($game->completed)
                                        @if($game->is_walkover)
                                            <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-medium text-amber-400">{{ __('messages.walkover') }}</span>
                                        @else
                                            <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-medium text-success">{{ __('messages.completed') }}</span>
                                        @endif
                                    @endif
                                </div>

                                {{-- Match Row --}}
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-1 items-center gap-2">
                                        <span class="{{ $game->completed && $game->player1_sets > $game->player2_sets ? 'font-semibold text-success' : '' }}">
                                            {{ $game->player1->name }}
                                        </span>
                                        <button
                                            wire:click="swapPlayers({{ $game->id }})"
                                            class="cursor-pointer rounded p-1 text-text-muted transition hover:bg-surface hover:text-text-secondary"
                                            title="Swap players"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-2" x-data="{
                                        set1P1: 0, set1P2: 0,
                                        set2P1: 0, set2P2: 0,
                                        set3P1: 0, set3P2: 0,
                                        showSet3: false,
                                        editing: false,
                                        get set1Winner() {
                                            const p1 = this.set1P1, p2 = this.set1P2;
                                            if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
                                            if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
                                            return 0;
                                        },
                                        get set2Winner() {
                                            const p1 = this.set2P1, p2 = this.set2P2;
                                            if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
                                            if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
                                            return 0;
                                        },
                                        get needsSet3() {
                                            return this.set1Winner !== 0 && this.set2Winner !== 0 && this.set1Winner !== this.set2Winner;
                                        },
                                        get p1Sets() {
                                            let sets = 0;
                                            if (this.set1Winner === 1) sets++;
                                            if (this.set2Winner === 1) sets++;
                                            if (this.showSet3 && this.set3P1 > this.set3P2) sets++;
                                            return sets;
                                        },
                                        get p2Sets() {
                                            let sets = 0;
                                            if (this.set1Winner === 2) sets++;
                                            if (this.set2Winner === 2) sets++;
                                            if (this.showSet3 && this.set3P2 > this.set3P1) sets++;
                                            return sets;
                                        },
                                        get p1Games() {
                                            return this.set1P1 + this.set2P1 + (this.showSet3 ? this.set3P1 : 0);
                                        },
                                        get p2Games() {
                                            return this.set1P2 + this.set2P2 + (this.showSet3 ? this.set3P2 : 0);
                                        },
                                        get canSave() {
                                            return this.p1Sets === 2 || this.p2Sets === 2;
                                        }
                                    }" x-effect="showSet3 = needsSet3">
                                        {{-- Completed match display with edit button --}}
                                        @if($game->completed)
                                            @if($game->is_walkover)
                                                <div x-show="!editing" class="flex items-center gap-2">
                                                    <span class="rounded-full bg-amber-500/20 px-3 py-1 font-semibold text-amber-400">
                                                        W.O.
                                                    </span>
                                                    <span class="text-sm text-text-muted">
                                                        {{ $game->walkoverWinner?->name }} {{ __('messages.wins') }}
                                                    </span>
                                                    <button
                                                        wire:click="clearWalkover({{ $game->id }})"
                                                        class="ml-2 cursor-pointer text-text-muted transition hover:text-danger"
                                                        title="Clear walkover"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @else
                                                <div x-show="!editing" class="flex items-center gap-2">
                                                    <span class="rounded-full bg-surface px-3 py-1 text-lg font-semibold">
                                                        {{ $game->player1_sets }} - {{ $game->player2_sets }}
                                                    </span>
                                                    <span class="text-sm text-text-muted">
                                                        @if($game->set_scores)
                                                            ({{ collect($game->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ') }})
                                                        @else
                                                            ({{ $game->player1_games }}-{{ $game->player2_games }})
                                                        @endif
                                                    </span>
                                                    <button
                                                        @click="editing = true"
                                                        class="ml-2 cursor-pointer text-text-muted transition hover:text-primary"
                                                        title="Edit result"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <div x-show="!editing" class="flex items-center gap-2" x-data="{ showWalkover: false }">
                                                <button
                                                    @click="editing = true"
                                                    class="cursor-pointer rounded-md border border-white/20 px-4 py-2 text-sm text-text-secondary transition hover:border-primary hover:text-primary"
                                                >
                                                    {{ __('messages.enter_result') }}
                                                </button>
                                                <div class="relative">
                                                    <button
                                                        @click="showWalkover = !showWalkover"
                                                        class="cursor-pointer rounded-md border border-amber-500/30 px-3 py-2 text-sm text-amber-400 transition hover:border-amber-400 hover:bg-amber-500/10"
                                                        title="Record walkover"
                                                    >
                                                        W.O.
                                                    </button>
                                                    <div
                                                        x-show="showWalkover"
                                                        x-cloak
                                                        @click.away="showWalkover = false"
                                                        class="absolute right-0 top-full z-10 mt-2 w-48 rounded-lg border border-white/10 bg-surface p-2 shadow-xl"
                                                    >
                                                        <p class="mb-2 px-2 text-xs text-text-muted">{{ __('messages.select_winner') }}</p>
                                                        <button
                                                            wire:click="recordWalkover({{ $game->id }}, {{ $game->player1_id }})"
                                                            @click="showWalkover = false"
                                                            class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                                                        >
                                                            {{ $game->player1->name }}
                                                        </button>
                                                        <button
                                                            wire:click="recordWalkover({{ $game->id }}, {{ $game->player2_id }})"
                                                            @click="showWalkover = false"
                                                            class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                                                        >
                                                            {{ $game->player2->name }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                            <div x-show="editing" x-cloak class="flex flex-col gap-3" @keydown.enter="if (canSave) { $wire.updateGameResult({{ $game->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }">
                                                <div class="flex items-center gap-4">
                                                    {{-- Set 1 --}}
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="text-xs text-text-muted">{{ __('messages.set') }} 1</span>
                                                        <div class="flex items-center gap-1">
                                                            <input
                                                                type="number"
                                                                x-model.number="set1P1"
                                                                min="0"
                                                                max="7"
                                                                class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                            <span class="text-text-muted">-</span>
                                                            <input
                                                                type="number"
                                                                x-model.number="set1P2"
                                                                min="0"
                                                                max="7"
                                                                class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                        </div>
                                                    </div>

                                                    {{-- Set 2 --}}
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="text-xs text-text-muted">{{ __('messages.set') }} 2</span>
                                                        <div class="flex items-center gap-1">
                                                            <input
                                                                type="number"
                                                                x-model.number="set2P1"
                                                                min="0"
                                                                max="7"
                                                                class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                            <span class="text-text-muted">-</span>
                                                            <input
                                                                type="number"
                                                                x-model.number="set2P2"
                                                                min="0"
                                                                max="7"
                                                                class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                        </div>
                                                    </div>

                                                    {{-- Set 3 (Tiebreak) --}}
                                                    <div x-show="showSet3" class="flex flex-col items-center gap-1">
                                                        <span class="text-xs text-secondary">{{ __('messages.set') }} 3</span>
                                                        <div class="flex items-center gap-1">
                                                            <input
                                                                type="number"
                                                                x-model.number="set3P1"
                                                                min="0"
                                                                class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                            <span class="text-text-muted">-</span>
                                                            <input
                                                                type="number"
                                                                x-model.number="set3P2"
                                                                min="0"
                                                                class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm"
                                                            >
                                                        </div>
                                                    </div>

                                                    {{-- Add Set 3 button --}}
                                                    <button
                                                        x-show="!showSet3"
                                                        @click="showSet3 = true"
                                                        class="cursor-pointer rounded border border-dashed border-white/20 px-2 py-1 text-xs text-text-muted transition hover:border-secondary hover:text-secondary"
                                                    >
                                                        + {{ __('messages.set') }} 3
                                                    </button>
                                                </div>

                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-text-muted">
                                                            {{ __('messages.result') }}: <span class="font-medium text-text-primary" x-text="p1Sets + ' - ' + p2Sets"></span>
                                                            (<span x-text="p1Games + '-' + p2Games"></span>)
                                                        </span>
                                                        <button
                                                            @click="if (canSave) { $wire.updateGameResult({{ $game->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }"
                                                            :disabled="!canSave"
                                                            :class="canSave ? 'cursor-pointer bg-primary hover:bg-primary/90' : 'cursor-not-allowed bg-gray-500 opacity-50'"
                                                            class="rounded px-3 py-1 text-sm font-medium text-white"
                                                        >
                                                            Save
                                                        </button>
                                                        <button
                                                            @click="editing = false; showSet3 = false; set1P1 = 0; set1P2 = 0; set2P1 = 0; set2P2 = 0; set3P1 = 0; set3P2 = 0;"
                                                            class="cursor-pointer text-text-muted hover:text-text-secondary"
                                                        >
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <span x-show="needsSet3 && (set3P1 === set3P2)" x-cloak class="text-xs text-amber-400">
                                                        {{ __('messages.set3_required') }}
                                                    </span>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="flex-1 text-right">
                                        <span class="{{ $game->completed && $game->player2_sets > $game->player1_sets ? 'font-semibold text-success' : '' }}">
                                            {{ $game->player2->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-white/20 bg-surface p-8 text-center">
                    <svg class="mx-auto mb-4 h-12 w-12 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-text-muted">{{ __('messages.no_matches_scheduled') }}</p>
                    @if($tournament->players->count() >= 2)
                        <button
                            wire:click="generateSchedule"
                            class="mt-4 cursor-pointer rounded-lg bg-primary px-6 py-2 font-medium text-white transition hover:bg-primary-hover"
                        >
                            {{ __('messages.generate_match_schedule') }}
                        </button>
                    @else
                        <p class="mt-2 text-sm text-text-muted">{{ __('messages.add_at_least_2') }}</p>
                    @endif
                </div>
            @endif
        @endif

        {{-- Players Slide-out Drawer --}}
        <template x-teleport="body">
            <div
                x-data="{ show: @entangle('showPlayersDrawer') }"
                x-show="show"
                x-cloak
                class="fixed inset-0 z-50"
                @keydown.escape.window="show && (show = false)"
            >
                {{-- Backdrop --}}
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="show = false"
                    class="fixed inset-0 bg-black/50"
                ></div>

                {{-- Drawer Panel --}}
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="fixed right-0 top-0 h-full w-full max-w-md overflow-y-auto bg-surface shadow-xl"
                >
                {{-- Drawer Header --}}
                <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                    <h3 class="text-lg font-semibold">{{ __('messages.manage_players') }}</h3>
                    <button
                        @click="show = false"
                        class="cursor-pointer rounded-lg p-2 text-text-muted transition hover:bg-surface-light hover:text-text-primary"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Current Players --}}
                    <div>
                        <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">
                            {{ __('messages.current_players') }} ({{ $tournament->players->count() }})
                        </h4>
                        @if($tournament->players->count() > 0)
                            <ul class="space-y-2">
                                @foreach($tournament->players as $player)
                                    <li wire:key="drawer-player-{{ $player->id }}" class="flex items-center justify-between rounded-lg border border-white/5 bg-surface-light px-4 py-3">
                                        <span>{{ $player->name }}</span>
                                        @if($tournament->games->isEmpty())
                                            <button
                                                wire:click="removePlayer({{ $player->id }})"
                                                class="cursor-pointer text-text-muted transition hover:text-danger"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-text-muted">{{ __('messages.no_players_added') }}</p>
                        @endif
                    </div>

                    {{-- Add New Player --}}
                    <div>
                        <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">{{ __('messages.add_new_player') }}</h4>
                        <form wire:submit="{{ $tournament->games->isEmpty() ? 'addPlayer' : 'addPlayerAndUpdateSchedule' }}" class="flex gap-2">
                            <input
                                type="text"
                                wire:model="newPlayerName"
                                class="flex-1 rounded-lg border border-white/10 bg-background px-4 py-2 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="{{ __('messages.player_name_placeholder') }}"
                            >
                            <button
                                type="submit"
                                class="cursor-pointer rounded-lg bg-primary px-4 py-2 font-medium text-white transition hover:bg-primary-hover"
                            >
                                {{ __('messages.add') }}
                            </button>
                        </form>
                    </div>

                    {{-- Quick Add from History --}}
                    @if($this->playerHistory->count() > 0)
                        <div class="border-t border-white/10 pt-6">
                            <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">{{ __('messages.quick_add_history') }}</h4>
                            <p class="mb-3 text-xs text-text-muted">{{ __('messages.players_from_other') }}</p>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($this->playerHistory as $historyPlayer)
                                    <label wire:key="history-{{ $historyPlayer->name }}" class="flex cursor-pointer items-center justify-between rounded-lg border border-white/5 bg-surface-light px-4 py-3 transition hover:border-white/20">
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                wire:model.live="selectedPlayers"
                                                value="{{ $historyPlayer->name }}"
                                                class="h-4 w-4 cursor-pointer rounded border-white/20 bg-background text-primary focus:ring-primary/20"
                                            >
                                            <span>{{ $historyPlayer->name }}</span>
                                        </div>
                                        <span class="text-xs text-text-muted">{{ $historyPlayer->tournament_count }}x</span>
                                    </label>
                                @endforeach
                            </div>
                            @if(count($selectedPlayers) > 0)
                                <button
                                    wire:click="importPlayers"
                                    class="mt-4 w-full cursor-pointer rounded-lg border border-primary bg-primary/10 px-4 py-2 font-medium text-primary transition hover:bg-primary hover:text-white"
                                >
                                    {{ __('messages.add_selected', ['count' => count($selectedPlayers)]) }}
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Generate Schedule (if in drawer and eligible) --}}
                    @if($tournament->games->isEmpty() && $tournament->players->count() >= 2)
                        <div class="border-t border-white/10 pt-6">
                            <button
                                wire:click="generateSchedule"
                                @click="show = false"
                                class="w-full cursor-pointer rounded-lg bg-success px-4 py-3 font-medium text-white transition hover:bg-success/90"
                            >
                                {{ __('messages.generate_match_schedule') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        </template>

        {{-- Edit Tournament Modal --}}
        @if($editingTournament)
            <template x-teleport="body">
                <div
                    class="fixed inset-0 z-50 flex items-center justify-center"
                    @keydown.escape.window="$wire.cancelEditingTournament()"
                >
                    {{-- Backdrop --}}
                    <div
                        wire:click="cancelEditingTournament"
                        class="fixed inset-0 bg-black/50"
                    ></div>

                    {{-- Modal Panel --}}
                    <div class="relative w-full max-w-xl rounded-2xl border border-white/10 bg-surface p-6 shadow-xl">
                        <h3 class="mb-6 text-xl font-semibold">{{ __('messages.edit_tournament') }}</h3>

                        <form wire:submit="updateTournament" class="space-y-5">
                            {{-- Tournament Name --}}
                            <div>
                                <label for="editName" class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_name') }}</label>
                                <input
                                    type="text"
                                    id="editName"
                                    wire:model="editName"
                                    class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                    placeholder="{{ __('messages.tournament_name') }}"
                                >
                                @error('editName') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Date Range --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_dates') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <input
                                            type="date"
                                            wire:model="editStartDate"
                                            class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        >
                                    </div>
                                    <span class="text-text-muted">{{ __('messages.to') }}</span>
                                    <div class="flex-1">
                                        <input
                                            type="date"
                                            wire:model="editEndDate"
                                            class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        >
                                    </div>
                                </div>
                                @error('editStartDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                                @error('editEndDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Format Selection --}}
                            <div>
                                <label class="mb-3 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_format') }}</label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="group cursor-pointer">
                                        <input type="radio" wire:model="editFormat" value="round_robin" class="peer hidden">
                                        <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                            <div class="mb-2 flex items-center gap-2">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </div>
                                                <span class="font-medium">{{ __('messages.round_robin') }}</span>
                                            </div>
                                            <p class="text-sm text-text-muted">{{ __('messages.round_robin_short') }}</p>
                                        </div>
                                    </label>

                                    <label class="group cursor-pointer">
                                        <input type="radio" wire:model="editFormat" value="round_robin_finals" class="peer hidden">
                                        <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                            <div class="mb-2 flex items-center gap-2">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                                    <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                    </svg>
                                                </div>
                                                <span class="font-medium">{{ __('messages.round_robin_finals') }}</span>
                                            </div>
                                            <p class="text-sm text-text-muted">{{ __('messages.round_robin_finals_short') }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    wire:click="cancelEditingTournament"
                                    class="cursor-pointer rounded-lg px-4 py-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                                >
                                    {{ __('messages.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    class="cursor-pointer rounded-lg bg-primary px-6 py-2 font-medium text-white transition hover:bg-primary-hover"
                                >
                                    {{ __('messages.save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        @endif
    @endif
</div>
