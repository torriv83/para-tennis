{{-- Tournament List for Visitors --}}
@php
    $today = now()->startOfDay();

    // Active tournament: today is between start_date and end_date (inclusive)
    $activeTournament = $this->allTournaments->first(function($t) use ($today) {
        $start = $t->start_date->startOfDay();
        $end = $t->end_date ? $t->end_date->startOfDay() : $start;
        return $today->greaterThanOrEqualTo($start) && $today->lessThanOrEqualTo($end);
    });

    // All other tournaments are "previous"
    $previousTournaments = $activeTournament
        ? $this->allTournaments->filter(fn($t) => $t->id !== $activeTournament->id)
        : $this->allTournaments;
@endphp

<div class="space-y-6">
    {{-- Admin Create Button - only show when no active tournament --}}
    @auth
        @if(!$activeTournament)
            <div class="flex justify-end">
                <a
                    href="{{ route('tournament.create') }}"
                    wire:navigate
                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-primary px-4 py-2 font-medium text-white transition hover:bg-primary-hover"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('messages.new_tournament') }}
                </a>
            </div>
        @endif
    @endauth

    @if($this->allTournaments->count() > 0)

        {{-- Active Tournament Section --}}
        @if($activeTournament)
            <div class="rounded-xl border border-primary/30 bg-gradient-to-br from-primary/10 to-transparent p-6">
                <div class="mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-sm font-medium text-primary">{{ __('messages.active_tournament') }}</h3>
                </div>

                <a
                    href="{{ route('home', $activeTournament) }}"
                    wire:navigate
                    class="block cursor-pointer rounded-lg border border-white/10 bg-surface p-5 transition hover:border-primary/50 hover:bg-surface-light"
                >
                    <div class="mb-3">
                        <h4 class="text-xl font-semibold">{{ $activeTournament->name }}</h4>
                        <p class="mt-1 text-sm text-text-muted">
                            {{ localized_date_range($activeTournament->start_date, $activeTournament->end_date) }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm text-text-secondary">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ $activeTournament->players_count }} {{ __('messages.players') }}</span>
                        </div>

                        <span class="text-text-muted">•</span>

                        @php
                            $totalGames = $activeTournament->games()->where('is_final', false)->where('is_doubles', false)->count();
                            $completedGames = $activeTournament->games()->where('is_final', false)->where('is_doubles', false)->where('completed', true)->count();
                        @endphp

                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>{{ $completedGames }}/{{ $totalGames }} {{ __('messages.matches_completed') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2 text-sm font-medium text-primary">
                        <span>{{ __('messages.view_tournament') }}</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        @endif

        {{-- Previous Tournaments Section --}}
        @if($previousTournaments->count() > 0)
            <div class="rounded-xl border border-white/10 bg-surface p-4 sm:p-6">
                <h3 class="mb-4 text-lg font-medium">{{ __('messages.previous_tournaments') }}</h3>
                <div class="space-y-2">
                    @foreach($previousTournaments as $t)
                        <a
                            wire:key="history-{{ $t->id }}"
                            href="{{ route('home', $t) }}"
                            wire:navigate
                            class="flex cursor-pointer flex-col gap-1 rounded-lg border border-white/5 bg-surface-light px-4 py-3 transition hover:border-white/20 sm:flex-row sm:items-center sm:justify-between sm:gap-2"
                        >
                            <div class="min-w-0 flex-1">
                                <span class="font-medium">{{ $t->name }}</span>
                                <span class="mt-1 block text-sm text-text-muted sm:ml-2 sm:mt-0 sm:inline">
                                    {{ localized_date_range($t->start_date, $t->end_date) }}
                                </span>
                            </div>
                            <span class="text-sm text-text-muted">
                                {{ $t->players_count }} {{ __('messages.players') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="rounded-xl border border-white/10 bg-surface p-8 text-center">
            <svg class="mx-auto h-16 w-16 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-text-secondary">{{ __('messages.no_tournaments') }}</h3>
            <p class="mt-2 text-sm text-text-muted">{{ __('messages.no_tournaments_desc') }}</p>
        </div>
    @endif
</div>
