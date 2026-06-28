{{-- Doubles Tab --}}
<div class="space-y-6 sm:space-y-8">
    {{-- Doubles Champion Banner --}}
    @if($this->doublesChampion)
        <div class="rounded-xl border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-transparent p-5 text-center sm:p-6">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-4 py-1.5 text-sm text-amber-400">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                {{ __('messages.doubles_champion') }}
            </div>
            <div class="text-2xl font-bold text-amber-400 sm:text-3xl">{{ $this->doublesChampion->displayName() }}</div>
        </div>
    @endif

    {{-- Teams Management --}}
    @include('livewire.tournament.partials.doubles-teams')

    @if($tournament->teams->count() >= 2)
        {{-- Generate Doubles Schedule CTA --}}
        @auth
            @if($this->doublesGames->isEmpty())
                <div class="rounded-xl border border-dashed border-primary/50 bg-primary/5 p-5 text-center sm:p-6">
                    <p class="mb-4 text-sm text-text-secondary sm:text-base">{{ __('messages.ready_to_start_doubles') }}</p>
                    <button
                        wire:click="generateDoublesSchedule"
                        class="cursor-pointer rounded-lg bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover"
                    >
                        {{ __('messages.generate_doubles_schedule') }}
                    </button>
                </div>
            @endif
        @endauth

        {{-- Standings, matches, and (for round_robin_finals) the final — final stays last --}}
        @if($this->doublesGames->isNotEmpty())
            @include('livewire.tournament.partials.doubles-standings')
            @include('livewire.tournament.partials.doubles-matches')

            @if($tournament->doubles_format === \App\TournamentFormat::RoundRobinFinals)
                @include('livewire.tournament.partials.doubles-final')
            @endif
        @endif
    @endif
</div>
