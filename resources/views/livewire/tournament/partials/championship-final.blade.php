{{-- Championship Final Section (only for round_robin_finals format) --}}
@if($tournament->format === \App\TournamentFormat::RoundRobinFinals)
    <div class="rounded-xl border border-primary/30 bg-gradient-to-br from-primary/10 via-surface to-surface p-6">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/20">
                <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('messages.championship_final') }}</h3>
                <p class="text-sm text-text-muted">{{ __('messages.top_2_compete') }}</p>
            </div>
        </div>

        @if($this->tournamentChampion)
            {{-- Champion Display --}}
            <div class="rounded-xl border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-transparent p-6 text-center">
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-4 py-1.5 text-amber-400">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.tournament_champion') }}
                </div>
                <div class="text-3xl font-bold text-amber-400">{{ $this->tournamentChampion->name }}</div>
                @if($this->finalMatch)
                    <div class="mt-2 text-text-muted">
                        @if($this->finalMatch->is_walkover)
                            {{ __('messages.won_by_walkover') }}
                        @else
                            @php
                                $isChampionPlayer1 = $this->finalMatch->player1_id === $this->tournamentChampion->id;
                                $opponentName = $isChampionPlayer1 ? $this->finalMatch->player2->name : $this->finalMatch->player1->name;
                                $winnerSets = $isChampionPlayer1 ? $this->finalMatch->player1_sets : $this->finalMatch->player2_sets;
                                $loserSets = $isChampionPlayer1 ? $this->finalMatch->player2_sets : $this->finalMatch->player1_sets;
                            @endphp
                            {{ __('messages.defeated') }} {{ $opponentName }} {{ $winnerSets }}-{{ $loserSets }}
                            @if($this->finalMatch->set_scores)
                                @php
                                    $setScoresDisplay = $isChampionPlayer1
                                        ? collect($this->finalMatch->set_scores)->map(fn($s) => $s[0].'-'.$s[1])->join(', ')
                                        : collect($this->finalMatch->set_scores)->map(fn($s) => $s[1].'-'.$s[0])->join(', ');
                                @endphp
                                ({{ $setScoresDisplay }})
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        @elseif($this->finalMatch)
            {{-- Final Match Card --}}
            @php $final = $this->finalMatch; @endphp
            <div class="rounded-lg border border-white/10 bg-surface-light p-4">
                <div class="flex items-center justify-between">
                    <div class="flex flex-1 items-center gap-3">
                        <div class="text-center">
                            <div class="text-xs text-text-muted">#1</div>
                            <div class="font-semibold {{ $final->completed && $final->player1_sets > $final->player2_sets ? 'text-success' : '' }}">
                                {{ $final->player1->name }}
                            </div>
                        </div>
                    </div>

                    @include('livewire.tournament.partials.score-entry', ['game' => $final, 'variant' => 'final'])

                    <div class="flex flex-1 items-center justify-end gap-3">
                        <div class="text-center">
                            <div class="text-xs text-text-muted">#2</div>
                            <div class="font-semibold {{ $final->completed && $final->player2_sets > $final->player1_sets ? 'text-success' : '' }}">
                                {{ $final->player2->name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($this->roundRobinComplete)
            {{-- This shouldn't happen - final should auto-create, but just in case --}}
            <div class="rounded-lg border border-dashed border-secondary/50 bg-secondary/5 p-4 text-center">
                <p class="text-text-muted">{{ __('messages.round_robin_complete_notice') }}</p>
            </div>
        @else
            {{-- Round Robin not complete yet --}}
            <div class="rounded-lg border border-dashed border-white/20 bg-surface/50 p-4 text-center">
                <p class="text-text-muted">{{ __('messages.complete_round_robin') }}</p>
                @php
                    $roundRobinGames = $tournament->games->where('is_final', false);
                    $completedCount = $roundRobinGames->where('completed', true)->count();
                    $totalCount = $roundRobinGames->count();
                @endphp
                @if($totalCount > 0)
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <div class="h-2 w-32 overflow-hidden rounded-full bg-surface-light">
                            <div class="h-full bg-secondary transition-all" style="width: {{ ($completedCount / $totalCount) * 100 }}%"></div>
                        </div>
                        <span class="text-sm text-text-muted">{{ $completedCount }}/{{ $totalCount }}</span>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endif
