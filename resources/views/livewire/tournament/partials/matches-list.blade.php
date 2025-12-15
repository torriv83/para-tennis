{{-- Matches Tab: Full match list --}}
@if($tournament->games->count() > 0)
    <div class="rounded-xl border border-white/10 bg-surface p-3 sm:p-6">
        <h3 class="mb-4 text-lg font-medium">{{ __('messages.all_matches') }}</h3>

        <div class="space-y-3">
            @foreach($tournament->games->where('is_final', false)->where('is_doubles', false)->sortBy('scheduled_at') as $game)
                @include('livewire.tournament.partials.match-card', ['game' => $game, 'tournament' => $tournament])
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
