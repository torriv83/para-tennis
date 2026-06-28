{{-- Doubles Matches List --}}
<div class="rounded-xl border border-white/10 bg-surface p-3 sm:p-6">
    <h3 class="mb-4 text-lg font-medium">{{ __('messages.doubles_matches') }}</h3>

    <div class="space-y-3">
        @foreach($this->doublesGames as $game)
            @include('livewire.tournament.partials.doubles-match-card', ['game' => $game, 'tournament' => $tournament])
        @endforeach
    </div>
</div>
