{{-- Doubles Teams Management (admin only) --}}
@auth
    <div class="rounded-xl border border-white/10 bg-surface p-4 sm:p-6">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h3 class="text-lg font-medium">{{ __('messages.doubles_teams') }} ({{ $tournament->teams->count() }})</h3>
        </div>

        @if($tournament->teams->count() > 0)
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach($tournament->teams as $team)
                    <div wire:key="team-{{ $team->id }}" class="flex items-center justify-between gap-2 rounded-lg border border-white/5 bg-surface-light px-4 py-3">
                        <span class="min-w-0 truncate text-sm font-medium sm:text-base">{{ $team->displayName() }}</span>
                        <button
                            wire:click="removeTeam({{ $team->id }})"
                            class="shrink-0 cursor-pointer text-text-muted transition hover:text-danger"
                            title="{{ __('messages.remove_team') }}"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-text-muted">{{ __('messages.no_teams_yet') }}</p>
        @endif

        <div class="mt-4 border-t border-white/10 pt-4">
            @if($this->availableDoublesPlayers->count() >= 2)
                @if($showTeamForm)
                    <div class="rounded-lg border border-primary/30 bg-primary/5 p-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-primary">{{ __('messages.player') }} 1</label>
                                <select wire:model="newTeamPlayer1" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                    <option value="">{{ __('messages.select_player') }}</option>
                                    @foreach($this->availableDoublesPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-primary">{{ __('messages.player') }} 2</label>
                                <select wire:model="newTeamPlayer2" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                    <option value="">{{ __('messages.select_player') }}</option>
                                    @foreach($this->availableDoublesPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button
                                wire:click="resetTeamForm"
                                class="cursor-pointer rounded-lg px-4 py-2 text-sm text-text-secondary transition hover:bg-surface-light"
                            >
                                {{ __('messages.cancel') }}
                            </button>
                            <button
                                wire:click="createTeam"
                                class="cursor-pointer rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-hover"
                            >
                                {{ __('messages.create_team') }}
                            </button>
                        </div>
                    </div>
                @else
                    <button
                        wire:click="$toggle('showTeamForm')"
                        class="w-full cursor-pointer rounded-lg border border-dashed border-primary/50 bg-primary/5 p-3 text-center text-primary transition hover:border-primary hover:bg-primary/10"
                    >
                        + {{ __('messages.create_team') }}
                    </button>
                @endif
            @else
                <p class="text-sm text-text-muted">{{ __('messages.need_more_doubles_players') }}</p>
            @endif
        </div>
    </div>
@endauth
