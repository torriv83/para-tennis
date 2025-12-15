{{--
    Match Card Component

    Required variables:
    - $game: The game model
    - $tournament: The tournament model (for date constraints)
--}}

<div wire:key="game-{{ $game->id }}-{{ $game->scheduled_at?->timestamp ?? 'none' }}-{{ $game->completed ? 'done' : 'pending' }}" class="rounded-lg border border-white/5 bg-surface-light p-3 sm:p-4">
    {{-- Schedule Row --}}
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-white/5 pb-3" x-data="{
        scheduledAt: '{{ $game->scheduled_at?->format('Y-m-d\TH:i') ?? '' }}',
        editingSchedule: false
    }">
        <div class="flex items-center gap-2 text-xs text-text-muted sm:text-sm">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            @auth
                <template x-if="!editingSchedule">
                    <span
                        @click="editingSchedule = true"
                        class="cursor-pointer hover:text-text-secondary"
                    >
                        @if($game->scheduled_at)
                            {{ localized_date($game->scheduled_at, 'datetime') }}
                        @else
                            {{ __('messages.set_schedule') }}
                        @endif
                    </span>
                </template>
                <template x-if="editingSchedule">
                    <div class="flex flex-wrap items-center gap-2">
                        <input
                            type="datetime-local"
                            x-model="scheduledAt"
                            min="{{ $tournament->start_date->format('Y-m-d\TH:i') }}"
                            max="{{ $tournament->end_date?->endOfDay()->format('Y-m-d\TH:i') ?? $tournament->start_date->endOfDay()->format('Y-m-d\TH:i') }}"
                            step="900"
                            @keydown.enter="$wire.updateGameSchedule({{ $game->id }}, scheduledAt); editingSchedule = false"
                            class="cursor-pointer rounded border border-white/20 bg-background px-2 py-1 text-xs sm:text-sm"
                        >
                        <button
                            @click="$wire.updateGameSchedule({{ $game->id }}, scheduledAt); editingSchedule = false"
                            class="cursor-pointer rounded bg-primary px-2 py-1 text-xs font-medium text-white"
                        >
                            Save
                        </button>
                        <button
                            @click="editingSchedule = false"
                            class="cursor-pointer text-text-muted hover:text-text-secondary"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
            @else
                <span>
                    @if($game->scheduled_at)
                        {{ localized_date($game->scheduled_at, 'datetime') }}
                    @else
                        -
                    @endif
                </span>
            @endauth
        </div>
        @if($game->completed)
            @if($game->is_walkover)
                <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-medium text-amber-400">{{ __('messages.walkover') }}</span>
            @else
                <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-medium text-success">{{ __('messages.completed') }}</span>
            @endif
        @endif
    </div>

    {{-- Match Row --}}
    <div class="flex items-center gap-4">
        <div class="flex flex-1 items-center gap-2">
            <span class="text-sm sm:text-base {{ $game->completed && $game->player1_sets > $game->player2_sets ? 'font-semibold text-success' : '' }}">
                {{ $game->player1->name }}
            </span>
            @auth
                <button
                    wire:click="swapPlayers({{ $game->id }})"
                    class="cursor-pointer rounded p-1 text-text-muted transition hover:bg-surface hover:text-text-secondary"
                    title="Swap players"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </button>
            @endauth
        </div>

        @include('livewire.tournament.partials.score-entry', ['game' => $game, 'variant' => 'default'])

        <div class="flex-1 text-right">
            <span class="text-sm sm:text-base {{ $game->completed && $game->player2_sets > $game->player1_sets ? 'font-semibold text-success' : '' }}">
                {{ $game->player2->name }}
            </span>
        </div>
    </div>
</div>
