{{-- Singles Standings --}}
@include('livewire.tournament.partials.standings-table', [
    'standings' => $this->standings,
    'competitorKey' => 'player',
    'title' => __('messages.standings'),
    'label' => __('messages.player'),
    'keyPrefix' => 'standing',
])
