{{-- Predictions Section --}}
<div class="rounded-xl border border-white/10 bg-surface p-4 sm:p-6" x-data="{ open: window.innerWidth >= 640 }">
    {{-- Mobile: Collapsible header --}}
    <button
        @click="open = !open"
        class="flex w-full cursor-pointer items-center justify-between gap-2 sm:cursor-default"
    >
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h3 class="text-lg font-medium">{{ __('messages.predictions') }}</h3>
        </div>
        {{-- Arrow only visible on mobile --}}
        <svg class="h-5 w-5 text-text-muted transition-transform duration-200 sm:hidden" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-collapse x-cloak class="sm:!block">
        @if(count($this->predictions) > 0)
            <div class="mt-4 space-y-3">
            @php
                // Sort predictions by best_position (ascending), then worst_position as tiebreaker
                $sortedPredictions = collect($this->predictions)
                    ->map(fn($pred, $playerId) => array_merge($pred, ['player_id' => $playerId]))
                    ->sortBy([
                        ['best_position', 'asc'],
                        ['worst_position', 'asc'],
                    ])
                    ->values();
            @endphp
            @foreach($sortedPredictions as $prediction)
                @php
                    $player = $tournament->players->firstWhere('id', $prediction['player_id']);
                @endphp
                @if($player)
                    <div wire:key="prediction-{{ $player->id }}" class="rounded-lg border border-white/5 bg-surface-light p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="mb-1 flex items-center gap-2">
                                    <span class="font-medium">{{ $player->name }}</span>
                                    @if($prediction['clinched'])
                                        <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-medium text-success">
                                            {{ __('messages.clinched') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-text-secondary">{{ $prediction['summary'] }}</p>
                            </div>
                            <div class="text-right">
                                @if($prediction['best_position'] === $prediction['worst_position'])
                                    <span class="text-2xl font-bold text-primary">{{ $prediction['best_position'] }}</span>
                                @else
                                    <span class="text-lg text-text-muted">
                                        {{ $prediction['best_position'] }}-{{ $prediction['worst_position'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if(count($prediction['scenarios']) > 1)
                            <div x-data="{ open: false }" class="mt-3 border-t border-white/5 pt-3">
                                <button
                                    @click="open = !open"
                                    class="cursor-pointer text-xs text-text-muted hover:text-text-secondary"
                                >
                                    <span x-text="open ? '{{ __('messages.hide_scenarios') }}' : '{{ __('messages.show_scenarios') }}'">{{ __('messages.show_scenarios') }}</span>
                                </button>
                                <div x-show="open" x-cloak class="mt-2 space-y-1">
                                    @foreach($prediction['scenarios'] as $scenario)
                                        <div class="flex items-start gap-2 text-sm">
                                            <span class="shrink-0 rounded bg-surface px-1.5 py-0.5 text-xs font-medium {{ $scenario['position'] <= 2 ? 'text-success' : 'text-text-muted' }}">
                                                {{ $scenario['position_label'] }}
                                            </span>
                                            <span class="text-text-secondary">
                                                {{ $scenario['conditions'] }}
                                                @if(!empty($scenario['external_conditions']))
                                                    <span class="text-text-muted">({{ __('messages.if') }} {{ $scenario['external_conditions'] }})</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
            </div>
        @else
            <p class="mt-4 text-text-muted">{{ __('messages.predictions_available_after') }}</p>
        @endif
    </div>
</div>
