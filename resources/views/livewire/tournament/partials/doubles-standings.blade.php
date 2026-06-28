{{-- Doubles Standings --}}
@include('livewire.tournament.partials.standings-table', [
    'standings' => $this->doublesStandings,
    'competitorKey' => 'team',
    'title' => __('messages.doubles_standings'),
    'label' => __('messages.team'),
    'keyPrefix' => 'doubles-standing',
])
