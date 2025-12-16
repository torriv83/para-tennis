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
        editingSchedule: false,
        showActionMenu: false,
        showWalkoverMenu: false
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
                            {{ __('messages.save') }}
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
        <div class="flex items-center gap-2">
            @if($game->completed)
                @if($game->is_walkover)
                    <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-medium text-amber-400">{{ __('messages.walkover') }}</span>
                @else
                    <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-medium text-success">{{ __('messages.completed') }}</span>
                @endif
            @endif

            {{-- Mobile: Action menu (only for incomplete matches) --}}
            @auth
                @if(!$game->completed)
                    <div class="relative sm:hidden">
                        <button
                            @click="showActionMenu = !showActionMenu"
                            class="cursor-pointer rounded-md p-1.5 text-text-muted transition hover:bg-surface hover:text-text-secondary"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="2"/>
                                <circle cx="12" cy="12" r="2"/>
                                <circle cx="12" cy="19" r="2"/>
                            </svg>
                        </button>
                        <div
                            x-show="showActionMenu"
                            x-cloak
                            @click.away="showActionMenu = false; showWalkoverMenu = false"
                            class="absolute right-0 top-full z-10 mt-2 w-48 rounded-lg border border-white/10 bg-surface p-2 shadow-xl"
                        >
                            <button
                                @click="$dispatch('start-editing-{{ $game->id }}'); showActionMenu = false"
                                class="flex w-full cursor-pointer items-center gap-2 rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ __('messages.enter_result') }}
                            </button>
                            <div class="relative">
                                <button
                                    @click="showWalkoverMenu = !showWalkoverMenu"
                                    class="flex w-full cursor-pointer items-center gap-2 rounded px-3 py-2 text-left text-sm text-amber-400 transition hover:bg-surface-light"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    W.O.
                                </button>
                                <div
                                    x-show="showWalkoverMenu"
                                    x-cloak
                                    class="mt-1 border-t border-white/10 pt-1"
                                >
                                    <p class="px-3 py-1 text-xs text-text-muted">{{ __('messages.select_winner') }}</p>
                                    <button
                                        wire:click="recordWalkover({{ $game->id }}, {{ $game->player1_id }})"
                                        @click="showActionMenu = false; showWalkoverMenu = false"
                                        class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                                    >
                                        {{ $game->player1->name }}
                                    </button>
                                    <button
                                        wire:click="recordWalkover({{ $game->id }}, {{ $game->player2_id }})"
                                        @click="showActionMenu = false; showWalkoverMenu = false"
                                        class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                                    >
                                        {{ $game->player2->name }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
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
