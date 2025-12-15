{{-- Generate Schedule CTA (if no games yet) --}}
@if($tournament->games->isEmpty() && $tournament->players->count() >= 2)
    @auth
        <div class="rounded-xl border border-dashed border-primary/50 bg-primary/5 p-6 text-center">
            <p class="mb-4 text-text-secondary">{{ __('messages.ready_to_start') }}</p>
            <button
                wire:click="generateSchedule"
                class="cursor-pointer rounded-lg bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover"
            >
                {{ __('messages.generate_match_schedule') }}
            </button>
        </div>
    @endauth
@elseif($tournament->players->count() < 2)
    <div class="rounded-xl border border-dashed border-white/20 bg-surface p-6 text-center">
        <p class="text-text-muted">{{ __('messages.add_players_first') }}</p>
        @auth
            <button
                wire:click="togglePlayersDrawer"
                class="mt-4 cursor-pointer text-primary hover:underline"
            >
                Manage Players
            </button>
        @endauth
    </div>
@endif
