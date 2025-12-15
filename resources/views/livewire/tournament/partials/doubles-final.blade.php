{{-- Doubles Final Section --}}
@if($tournament->has_doubles)
    <div class="rounded-xl border border-white/10 bg-surface p-6" x-data="{
        set1P1: 0, set1P2: 0,
        set2P1: 0, set2P2: 0,
        set3P1: 0, set3P2: 0,
        showSet3: false,
        editing: false,
        get set1Winner() {
            const p1 = this.set1P1, p2 = this.set1P2;
            if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
            if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
            return 0;
        },
        get set2Winner() {
            const p1 = this.set2P1, p2 = this.set2P2;
            if (p1 >= 6 && (p1 - p2 >= 2 || p1 === 7)) return 1;
            if (p2 >= 6 && (p2 - p1 >= 2 || p2 === 7)) return 2;
            return 0;
        },
        get needsSet3() {
            return this.set1Winner !== 0 && this.set2Winner !== 0 && this.set1Winner !== this.set2Winner;
        },
        get p1Sets() {
            let sets = 0;
            if (this.set1Winner === 1) sets++;
            if (this.set2Winner === 1) sets++;
            if (this.showSet3 && this.set3P1 > this.set3P2) sets++;
            return sets;
        },
        get p2Sets() {
            let sets = 0;
            if (this.set1Winner === 2) sets++;
            if (this.set2Winner === 2) sets++;
            if (this.showSet3 && this.set3P2 > this.set3P1) sets++;
            return sets;
        },
        get p1Games() {
            return this.set1P1 + this.set2P1 + (this.showSet3 ? this.set3P1 : 0);
        },
        get p2Games() {
            return this.set1P2 + this.set2P2 + (this.showSet3 ? this.set3P2 : 0);
        },
        get canSave() {
            return this.p1Sets === 2 || this.p2Sets === 2;
        },
        resetForm() {
            this.editing = false;
            this.showSet3 = false;
            this.set1P1 = 0; this.set1P2 = 0;
            this.set2P1 = 0; this.set2P2 = 0;
            this.set3P1 = 0; this.set3P2 = 0;
        }
    }" x-effect="showSet3 = needsSet3">
        {{-- Header with title and edit button --}}
        <div class="mb-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">{{ __('messages.doubles_final') }}</h3>
                    <p class="text-sm text-text-muted">{{ __('messages.doubles_final_desc') }}</p>
                </div>
            </div>
            {{-- Edit button - only show when match exists, completed, and not walkover --}}
            @auth
                @if($this->doublesMatch && $this->doublesMatch->completed && !$this->doublesMatch->is_walkover)
                    <button x-show="!editing" @click="editing = true" class="cursor-pointer text-text-muted transition hover:text-primary" title="Edit result">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                @endif
            @endauth
        </div>

        @if($this->doublesMatch)
            @php $doubles = $this->doublesMatch; @endphp
            {{-- Doubles Match Card --}}
            <div class="rounded-lg border border-white/10 bg-surface-light p-4">
                <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
                    {{-- Team 1 --}}
                    <div class="w-full text-center sm:flex-1 sm:text-left">
                        <div class="text-sm font-semibold sm:text-base {{ $doubles->completed && $doubles->player1_sets > $doubles->player2_sets ? 'text-success' : '' }}">
                            {{ $doubles->team1Names() }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($doubles->completed)
                            @if($doubles->is_walkover)
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full bg-amber-500/20 px-3 py-1 font-semibold text-amber-400">W.O.</span>
                                    <span class="text-sm text-text-muted">
                                        {{ $doubles->walkover_winner_id === $doubles->player1_id ? $doubles->team1Names() : $doubles->team2Names() }} {{ __('messages.wins') }}
                                    </span>
                                </div>
                            @else
                                <div x-show="!editing" class="flex flex-col items-center gap-1">
                                    {{-- Set scores above --}}
                                    <span class="text-sm text-text-muted">
                                        @if($doubles->set_scores)
                                            ({{ collect($doubles->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ') }})
                                        @else
                                            ({{ $doubles->player1_games }}-{{ $doubles->player2_games }})
                                        @endif
                                    </span>
                                    {{-- Main result below --}}
                                    <span class="rounded-full bg-primary/20 px-4 py-2 text-xl font-bold text-primary">
                                        {{ $doubles->player1_sets }} - {{ $doubles->player2_sets }}
                                    </span>
                                </div>
                            @endif
                        @else
                            @auth
                                <div x-show="!editing" class="flex items-center gap-2">
                                    <button @click="editing = true" class="cursor-pointer rounded-md border border-primary/50 bg-primary/10 px-4 py-2 text-sm font-medium text-primary transition hover:bg-primary/20">
                                        {{ __('messages.enter_result') }}
                                    </button>
                                    <div class="relative" x-data="{ showWalkover: false }">
                                        <button @click="showWalkover = !showWalkover" class="cursor-pointer rounded-md border border-amber-500/30 px-3 py-2 text-sm text-amber-400 transition hover:border-amber-400 hover:bg-amber-500/10" title="Record walkover">
                                            W.O.
                                        </button>
                                        <div x-show="showWalkover" x-cloak @click.away="showWalkover = false" class="absolute right-0 top-full z-10 mt-2 w-48 rounded-lg border border-white/10 bg-surface p-2 shadow-xl">
                                            <p class="mb-2 px-2 text-xs text-text-muted">{{ __('messages.select_winning_team') }}</p>
                                            <button wire:click="recordWalkover({{ $doubles->id }}, {{ $doubles->player1_id }})" @click="showWalkover = false" class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light">
                                                {{ $doubles->team1Names() }}
                                            </button>
                                            <button wire:click="recordWalkover({{ $doubles->id }}, {{ $doubles->player2_id }})" @click="showWalkover = false" class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light">
                                                {{ $doubles->team2Names() }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-text-muted">{{ __('messages.pending') }}</span>
                            @endauth
                        @endif

                        {{-- Score Entry Form --}}
                        @auth
                            <div x-show="editing" x-cloak class="flex flex-col gap-3" @keydown.enter="if (canSave) { $wire.updateGameResult({{ $doubles->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }">
                                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-text-muted">{{ __('messages.set') }} 1</span>
                                        <div class="flex items-center gap-1">
                                            <input type="number" x-model.number="set1P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                            <span class="text-text-muted">-</span>
                                            <input type="number" x-model.number="set1P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-text-muted">{{ __('messages.set') }} 2</span>
                                        <div class="flex items-center gap-1">
                                            <input type="number" x-model.number="set2P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                            <span class="text-text-muted">-</span>
                                            <input type="number" x-model.number="set2P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                                        </div>
                                    </div>
                                    <div x-show="showSet3" class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-primary">{{ __('messages.set') }} 3</span>
                                        <div class="flex items-center gap-1">
                                            <input type="number" x-model.number="set3P1" min="0" class="w-10 rounded border border-primary/50 bg-background px-1 py-1 text-center text-sm">
                                            <span class="text-text-muted">-</span>
                                            <input type="number" x-model.number="set3P2" min="0" class="w-10 rounded border border-primary/50 bg-background px-1 py-1 text-center text-sm">
                                        </div>
                                    </div>
                                    <button x-show="!showSet3" @click="showSet3 = true" class="cursor-pointer rounded border border-dashed border-white/20 px-2 py-1 text-xs text-text-muted transition hover:border-primary hover:text-primary">
                                        + {{ __('messages.set') }} 3
                                    </button>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-text-muted">
                                            {{ __('messages.result') }}: <span class="font-medium text-text-primary" x-text="p1Sets + ' - ' + p2Sets"></span>
                                            (<span x-text="p1Games + '-' + p2Games"></span>)
                                        </span>
                                        <button
                                            @click="if (canSave) { $wire.updateGameResult({{ $doubles->id }}, p1Sets, p2Sets, p1Games, p2Games, [[set1P1, set1P2], [set2P1, set2P2], ...(showSet3 ? [[set3P1, set3P2]] : [])]); editing = false; }"
                                            :disabled="!canSave"
                                            :class="canSave ? 'cursor-pointer bg-primary hover:bg-primary-hover' : 'cursor-not-allowed bg-gray-500 opacity-50'"
                                            class="rounded px-3 py-1 text-sm font-medium text-white"
                                        >
                                            {{ __('messages.save') }}
                                        </button>
                                        <button @click="resetForm()" class="cursor-pointer text-text-muted hover:text-text-secondary">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <span x-show="needsSet3 && (set3P1 === set3P2)" x-cloak class="text-xs text-amber-400">
                                        {{ __('messages.set3_required') }}
                                    </span>
                                </div>
                            </div>
                        @endauth
                    </div>

                    {{-- Team 2 --}}
                    <div class="w-full text-center sm:flex-1 sm:text-right">
                        <div class="text-sm font-semibold sm:text-base {{ $doubles->completed && $doubles->player2_sets > $doubles->player1_sets ? 'text-success' : '' }}">
                            {{ $doubles->team2Names() }}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($tournament->players->count() >= 4)
            @auth
                {{-- Create Doubles Match Form --}}
                @if($showDoublesForm)
                    <div class="rounded-lg border border-primary/30 bg-primary/5 p-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- Team 1 --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-primary">{{ __('messages.team_1') }}</label>
                                <div class="space-y-2">
                                    <select wire:model="doublesTeam1Player1" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                        <option value="">{{ __('messages.select_player') }}</option>
                                        @foreach($tournament->players as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                    <select wire:model="doublesTeam1Player2" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                        <option value="">{{ __('messages.select_player') }}</option>
                                        @foreach($tournament->players as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Team 2 --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-primary">{{ __('messages.team_2') }}</label>
                                <div class="space-y-2">
                                    <select wire:model="doublesTeam2Player1" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                        <option value="">{{ __('messages.select_player') }}</option>
                                        @foreach($tournament->players as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                    <select wire:model="doublesTeam2Player2" class="w-full cursor-pointer rounded-lg border border-white/10 bg-background px-3 py-2">
                                        <option value="">{{ __('messages.select_player') }}</option>
                                        @foreach($tournament->players as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button
                                wire:click="resetDoublesForm"
                                class="cursor-pointer rounded-lg px-4 py-2 text-sm text-text-secondary transition hover:bg-surface"
                            >
                                {{ __('messages.cancel') }}
                            </button>
                            <button
                                wire:click="createDoublesMatch"
                                class="cursor-pointer rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-hover"
                            >
                                {{ __('messages.create') }}
                            </button>
                        </div>
                    </div>
                @else
                    <button
                        wire:click="$toggle('showDoublesForm')"
                        class="w-full cursor-pointer rounded-lg border border-dashed border-primary/50 bg-primary/5 p-4 text-center transition hover:border-primary hover:bg-primary/10"
                    >
                        <span class="text-primary">+ {{ __('messages.create_doubles_final') }}</span>
                    </button>
                @endif
            @endauth
        @else
            <div class="rounded-lg border border-dashed border-white/20 bg-surface/50 p-4 text-center">
                <p class="text-text-muted">{{ __('messages.need_4_players') }}</p>
            </div>
        @endif
    </div>
@endif
