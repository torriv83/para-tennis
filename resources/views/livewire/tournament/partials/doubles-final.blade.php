{{-- Doubles Championship Final (only for round_robin_finals doubles format) --}}
<div class="rounded-xl border border-secondary/30 bg-gradient-to-br from-secondary/10 via-surface to-surface p-4 sm:p-6">
    <div class="mb-4 flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/20">
            <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold">{{ __('messages.doubles_final') }}</h3>
            <p class="text-sm text-text-muted">{{ __('messages.top_2_teams_compete') }}</p>
        </div>
    </div>

    @if($this->doublesFinalMatch)
        @php $final = $this->doublesFinalMatch; @endphp
        <div class="rounded-lg border border-white/10 bg-surface-light p-4">
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="flex-1">
                    <div class="text-xs text-text-muted">#1</div>
                    <div class="text-sm font-semibold sm:text-base {{ $final->completed && $final->player1_sets > $final->player2_sets ? 'text-success' : '' }}">
                        {{ $final->team1Names() }}
                    </div>
                </div>

                @include('livewire.tournament.partials.score-entry', ['game' => $final, 'variant' => 'doubles'])

                <div class="flex-1 text-right">
                    <div class="text-xs text-text-muted">#2</div>
                    <div class="text-sm font-semibold sm:text-base {{ $final->completed && $final->player2_sets > $final->player1_sets ? 'text-success' : '' }}">
                        {{ $final->team2Names() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Round robin not complete yet --}}
        <div class="rounded-lg border border-dashed border-white/20 bg-surface/50 p-4 text-center">
            <p class="text-text-muted">{{ __('messages.complete_doubles_round_robin') }}</p>
            @php
                $completedCount = $this->doublesGames->where('completed', true)->count();
                $totalCount = $this->doublesGames->count();
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
