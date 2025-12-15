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
