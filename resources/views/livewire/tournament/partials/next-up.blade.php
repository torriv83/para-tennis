{{-- Next Up Widget --}}
@if($this->nextUp['type'] !== 'none')
    <div class="rounded-xl border border-primary/30 bg-gradient-to-r from-primary/10 to-transparent p-3 sm:p-4">
        <div class="mb-3 flex items-center gap-2">
            @if($this->nextUp['type'] === 'today')
                <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            @else
                <svg class="h-5 w-5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            @endif
            <h3 class="text-sm font-medium sm:text-base {{ $this->nextUp['type'] === 'today' ? 'text-primary' : 'text-text-muted' }}">{{ $this->nextUp['type'] === 'today' ? __('messages.playing_today') : __('messages.next_match') }}</h3>
        </div>
        <div class="space-y-2">
            @foreach($this->nextUp['games'] as $game)
                <div wire:key="nextup-{{ $game->id }}" class="flex items-center justify-between gap-2 rounded-lg bg-surface/50 p-3">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm sm:text-base">
                        <span class="font-medium">{{ $game->player1->name }}</span>
                        <span class="text-text-muted">{{ __('messages.vs') }}</span>
                        <span class="font-medium">{{ $game->player2->name }}</span>
                    </div>
                    <span class="shrink-0 text-xs text-text-muted sm:text-sm">{{ localized_date($game->scheduled_at, 'time') }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
