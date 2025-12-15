{{--
    Score Entry Component

    Required variables:
    - $game: The game model with player1, player2, completed, set_scores, etc.

    Optional variables:
    - $variant: 'default' | 'final' | 'doubles' - affects button styling (default: 'default')
    - $showWalkover: Whether to show walkover button (default: true)
    - $showEditButton: Whether to show edit button for completed matches (default: true)
--}}

@php
    $variant = $variant ?? 'default';
    $showWalkover = $showWalkover ?? true;
    $showEditButton = $showEditButton ?? true;

    $enterButtonClass = match($variant) {
        'final', 'doubles' => 'cursor-pointer rounded-md border border-secondary/50 bg-secondary/10 px-4 py-2 text-sm font-medium text-secondary transition hover:bg-secondary/20',
        default => 'cursor-pointer rounded-md border border-white/20 px-4 py-2 text-sm text-text-secondary transition hover:border-primary hover:text-primary',
    };

    $scoreDisplayClass = match($variant) {
        'final', 'doubles' => 'rounded-full bg-secondary/20 px-4 py-2 text-xl font-bold text-secondary',
        default => 'rounded-full bg-surface px-3 py-1 text-lg font-semibold',
    };

    $enterButtonText = match($variant) {
        'final' => __('messages.enter_final_result'),
        default => __('messages.enter_result'),
    };
@endphp

<div class="flex items-center gap-2" x-data="{
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
    },
    saveResult() {
        if (this.canSave) {
            const setScores = [[this.set1P1, this.set1P2], [this.set2P1, this.set2P2]];
            if (this.showSet3) setScores.push([this.set3P1, this.set3P2]);
            $wire.updateGameResult({{ $game->id }}, this.p1Sets, this.p2Sets, this.p1Games, this.p2Games, setScores);
            this.editing = false;
        }
    }
}" x-effect="showSet3 = needsSet3">
    {{-- Completed match display --}}
    @if($game->completed)
        @if($game->is_walkover)
            <div x-show="!editing" class="flex items-center gap-2">
                <span class="rounded-full bg-amber-500/20 px-3 py-1 font-semibold text-amber-400">W.O.</span>
                <span class="text-sm text-text-muted">{{ $game->walkoverWinner?->name }} {{ __('messages.wins') }}</span>
                @auth
                    @if($showEditButton)
                        <button
                            wire:click="clearWalkover({{ $game->id }})"
                            class="ml-2 cursor-pointer text-text-muted transition hover:text-danger"
                            title="Clear walkover"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                @endauth
            </div>
        @else
            <div x-show="!editing" class="flex items-center gap-2">
                <span class="{{ $scoreDisplayClass }}">
                    {{ $game->player1_sets }} - {{ $game->player2_sets }}
                </span>
                <span class="text-sm text-text-muted">
                    @if($game->set_scores)
                        ({{ collect($game->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ') }})
                    @else
                        ({{ $game->player1_games }}-{{ $game->player2_games }})
                    @endif
                </span>
                @auth
                    @if($showEditButton)
                        <button @click="editing = true" class="ml-2 cursor-pointer text-text-muted transition hover:text-primary" title="Edit result">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    @endif
                @endauth
            </div>
        @endif
    @else
        {{-- Not completed - show enter result button --}}
        @auth
            <div x-show="!editing" class="flex items-center gap-2" x-data="{ showWalkoverMenu: false }">
                <button @click="editing = true" class="{{ $enterButtonClass }}">
                    {{ $enterButtonText }}
                </button>
                @if($showWalkover)
                    <div class="relative">
                        <button
                            @click="showWalkoverMenu = !showWalkoverMenu"
                            class="cursor-pointer rounded-md border border-amber-500/30 px-3 py-2 text-sm text-amber-400 transition hover:border-amber-400 hover:bg-amber-500/10"
                            title="Record walkover"
                        >
                            W.O.
                        </button>
                        <div
                            x-show="showWalkoverMenu"
                            x-cloak
                            @click.away="showWalkoverMenu = false"
                            class="absolute right-0 top-full z-10 mt-2 w-48 rounded-lg border border-white/10 bg-surface p-2 shadow-xl"
                        >
                            <p class="mb-2 px-2 text-xs text-text-muted">{{ __('messages.select_winner') }}</p>
                            <button
                                wire:click="recordWalkover({{ $game->id }}, {{ $game->player1_id }})"
                                @click="showWalkoverMenu = false"
                                class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                            >
                                {{ $game->player1->name }}
                            </button>
                            <button
                                wire:click="recordWalkover({{ $game->id }}, {{ $game->player2_id }})"
                                @click="showWalkoverMenu = false"
                                class="w-full cursor-pointer rounded px-3 py-2 text-left text-sm transition hover:bg-surface-light"
                            >
                                {{ $game->player2->name }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <span class="text-sm text-text-muted">{{ __('messages.pending') }}</span>
        @endauth
    @endif

    {{-- Score Entry Form --}}
    @auth
        <div x-show="editing" x-cloak class="flex flex-col gap-3" @keydown.enter="saveResult()">
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                {{-- Set 1 --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs text-text-muted">{{ __('messages.set') }} 1</span>
                    <div class="flex items-center gap-1">
                        <input type="number" x-model.number="set1P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                        <span class="text-text-muted">-</span>
                        <input type="number" x-model.number="set1P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                    </div>
                </div>
                {{-- Set 2 --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs text-text-muted">{{ __('messages.set') }} 2</span>
                    <div class="flex items-center gap-1">
                        <input type="number" x-model.number="set2P1" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                        <span class="text-text-muted">-</span>
                        <input type="number" x-model.number="set2P2" min="0" max="7" class="w-10 rounded border border-white/20 bg-background px-1 py-1 text-center text-sm">
                    </div>
                </div>
                {{-- Set 3 (conditional) --}}
                <div x-show="showSet3" class="flex flex-col items-center gap-1">
                    <span class="text-xs text-secondary">{{ __('messages.set') }} 3</span>
                    <div class="flex items-center gap-1">
                        <input type="number" x-model.number="set3P1" min="0" class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm">
                        <span class="text-text-muted">-</span>
                        <input type="number" x-model.number="set3P2" min="0" class="w-10 rounded border border-secondary/50 bg-background px-1 py-1 text-center text-sm">
                    </div>
                </div>
                {{-- Add Set 3 button --}}
                <button x-show="!showSet3" @click="showSet3 = true" class="cursor-pointer rounded border border-dashed border-white/20 px-2 py-1 text-xs text-text-muted transition hover:border-secondary hover:text-secondary">
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
                        @click="saveResult()"
                        :disabled="!canSave"
                        :class="canSave ? 'cursor-pointer bg-secondary hover:bg-secondary/90' : 'cursor-not-allowed bg-gray-500 opacity-50'"
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
