{{-- Players Slide-out Drawer --}}
<template x-teleport="body">
    <div
        x-data="{ show: @entangle('showPlayersDrawer') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50"
        @keydown.escape.window="show && (show = false)"
    >
        {{-- Backdrop --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="show = false"
            class="fixed inset-0 bg-black/50"
        ></div>

        {{-- Drawer Panel --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed right-0 top-0 h-full w-full max-w-md overflow-y-auto bg-surface shadow-xl"
        >
            {{-- Drawer Header --}}
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                <h3 class="text-lg font-semibold">{{ __('messages.manage_players') }}</h3>
                <button
                    @click="show = false"
                    class="cursor-pointer rounded-lg p-2 text-text-muted transition hover:bg-surface-light hover:text-text-primary"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6 p-6">
                {{-- Current Players --}}
                <div>
                    <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">
                        {{ __('messages.current_players') }} ({{ $tournament->players->count() }})
                    </h4>
                    @if($tournament->players->count() > 0)
                        <ul class="space-y-2">
                            @foreach($tournament->players as $player)
                                <li wire:key="drawer-player-{{ $player->id }}" class="flex items-center justify-between rounded-lg border border-white/5 bg-surface-light px-4 py-3">
                                    <span>{{ $player->name }}</span>
                                    @if($tournament->games->isEmpty())
                                        <button
                                            wire:click="removePlayer({{ $player->id }})"
                                            class="cursor-pointer text-text-muted transition hover:text-danger"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-text-muted">{{ __('messages.no_players_added') }}</p>
                    @endif
                </div>

                {{-- Add New Player --}}
                <div>
                    <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">{{ __('messages.add_new_player') }}</h4>
                    <form wire:submit="{{ $tournament->games->isEmpty() ? 'addPlayer' : 'addPlayerAndUpdateSchedule' }}" class="flex gap-2">
                        <input
                            type="text"
                            wire:model="newPlayerName"
                            class="flex-1 rounded-lg border border-white/10 bg-background px-4 py-2 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            placeholder="{{ __('messages.player_name_placeholder') }}"
                        >
                        <button
                            type="submit"
                            class="cursor-pointer rounded-lg bg-primary px-4 py-2 font-medium text-white transition hover:bg-primary-hover"
                        >
                            {{ __('messages.add') }}
                        </button>
                    </form>
                </div>

                {{-- Quick Add from History --}}
                @if($this->playerHistory->count() > 0)
                    <div class="border-t border-white/10 pt-6">
                        <h4 class="mb-3 text-sm font-medium uppercase tracking-wide text-text-muted">{{ __('messages.quick_add_history') }}</h4>
                        <p class="mb-3 text-xs text-text-muted">{{ __('messages.players_from_other') }}</p>
                        <div class="max-h-48 space-y-2 overflow-y-auto">
                            @foreach($this->playerHistory as $historyPlayer)
                                <label wire:key="history-{{ $historyPlayer->name }}" class="flex cursor-pointer items-center justify-between rounded-lg border border-white/5 bg-surface-light px-4 py-3 transition hover:border-white/20">
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedPlayers"
                                            value="{{ $historyPlayer->name }}"
                                            class="h-4 w-4 cursor-pointer rounded border-white/20 bg-background text-primary focus:ring-primary/20"
                                        >
                                        <span>{{ $historyPlayer->name }}</span>
                                    </div>
                                    <span class="text-xs text-text-muted">{{ $historyPlayer->tournament_count }}x</span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($selectedPlayers) > 0)
                            <button
                                wire:click="importPlayers"
                                class="mt-4 w-full cursor-pointer rounded-lg border border-primary bg-primary/10 px-4 py-2 font-medium text-primary transition hover:bg-primary hover:text-white"
                            >
                                {{ __('messages.add_selected', ['count' => count($selectedPlayers)]) }}
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Generate Schedule (if in drawer and eligible) --}}
                @if($tournament->games->isEmpty() && $tournament->players->count() >= 2)
                    <div class="border-t border-white/10 pt-6">
                        <button
                            wire:click="generateSchedule"
                            @click="show = false"
                            class="w-full cursor-pointer rounded-lg bg-success px-4 py-3 font-medium text-white transition hover:bg-success/90"
                        >
                            {{ __('messages.generate_match_schedule') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</template>
