<div class="space-y-8">
    @if(!$tournament)
        {{-- Create Tournament View --}}
        @include('livewire.tournament.partials.create-tournament')
    @else
        {{-- Tournament Header (Navigation + Info) --}}
        @include('livewire.tournament.partials.tournament-header')

        {{-- Tab Bar --}}
        @include('livewire.tournament.partials.tabs')

        {{-- Tab Content --}}
        @if($activeTab === 'overview')
            {{-- Overview Tab: Next Up, Standings + Predictions, Finals --}}

            @include('livewire.tournament.partials.next-up')

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                @include('livewire.tournament.partials.standings')
                @include('livewire.tournament.partials.predictions')
            </div>

            {{-- Round Robin Champion (only for round_robin format) --}}
            @include('livewire.tournament.partials.round-robin-champion')

            {{-- Championship Final (only for round_robin_finals format) --}}
            @include('livewire.tournament.partials.championship-final')

            {{-- Doubles Final --}}
            @include('livewire.tournament.partials.doubles-final')

            {{-- Generate Schedule CTA --}}
            @include('livewire.tournament.partials.generate-schedule')

        @elseif($activeTab === 'matches')
            {{-- Matches Tab: Full match list --}}
            @include('livewire.tournament.partials.matches-list')
        @endif

        {{-- Players Drawer --}}
        @include('livewire.tournament.partials.players-drawer')

        {{-- Edit Tournament Modal --}}
        @include('livewire.tournament.partials.edit-tournament-modal')
    @endif
</div>
