{{-- Standings Section --}}
<div class="rounded-xl border border-white/10 bg-surface p-4 sm:p-6">
    <h3 class="mb-4 text-lg font-medium">{{ __('messages.standings') }}</h3>

    @if(count($this->standings) > 0)
        <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
            <table class="w-full text-sm sm:text-base">
                <thead>
                    <tr class="border-b border-white/10 text-left text-[10px] uppercase tracking-wide text-text-muted sm:text-xs">
                        <th class="pb-2 pr-1 sm:pb-3 sm:pr-3">#</th>
                        <th class="pb-2 pr-2 sm:pb-3 sm:pr-4">{{ __('messages.player') }}</th>
                        <th class="hidden cursor-help pb-3 pr-4 text-center sm:table-cell" title="{{ __('messages.played_full') }}">{{ __('messages.played_abbr') }}</th>
                        <th class="hidden cursor-help pb-3 pr-4 text-center sm:table-cell" title="{{ __('messages.wins_full') }}">{{ __('messages.wins_abbr') }}</th>
                        <th class="hidden cursor-help pb-3 pr-4 text-center sm:table-cell" title="{{ __('messages.losses_full') }}">{{ __('messages.losses_abbr') }}</th>
                        <th class="cursor-help pb-2 pr-2 text-center sm:pb-3 sm:pr-4" title="{{ __('messages.points_full') }}">{{ __('messages.points_abbr') }}</th>
                        <th class="cursor-help pb-2 pr-2 text-center sm:pb-3 sm:pr-4" title="{{ __('messages.sets_full') }}">{{ __('messages.sets_abbr') }}</th>
                        <th class="cursor-help pb-2 text-center sm:pb-3" title="{{ __('messages.games_full') }}">{{ __('messages.games_abbr') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->standings as $index => $standing)
                        @php
                            $setDiff = $standing['sets_won'] - $standing['sets_lost'];
                            $gameDiff = $standing['games_won'] - $standing['games_lost'];
                        @endphp
                        <tr wire:key="standing-{{ $standing['player']->id }}" class="border-b border-white/5">
                            <td class="py-2 pr-1 text-xs text-text-secondary sm:py-3 sm:pr-3 sm:text-sm">{{ $index + 1 }}</td>
                            <td class="max-w-[90px] truncate py-2 pr-2 text-xs font-medium sm:max-w-none sm:py-3 sm:pr-4 sm:text-sm">{{ $standing['player']->name }}</td>
                            <td class="hidden py-3 pr-4 text-center text-text-secondary sm:table-cell">{{ $standing['played'] }}</td>
                            <td class="hidden py-3 pr-4 text-center text-success sm:table-cell">{{ $standing['wins'] }}</td>
                            <td class="hidden py-3 pr-4 text-center text-danger sm:table-cell">{{ $standing['losses'] }}</td>
                            <td class="py-2 pr-2 text-center text-xs font-bold text-primary sm:py-3 sm:pr-4 sm:text-sm sm:font-semibold sm:text-amber-400">{{ $standing['points'] }}</td>
                            <td class="whitespace-nowrap py-2 pr-2 text-center sm:py-3 sm:pr-4">
                                <span class="text-xs {{ $setDiff > 0 ? 'text-success' : ($setDiff < 0 ? 'text-danger' : 'text-text-secondary') }} sm:hidden">{{ $setDiff > 0 ? '+' : '' }}{{ $setDiff }}</span>
                                <span class="hidden text-text-secondary sm:inline">
                                    {{ $standing['sets_won'] }}-{{ $standing['sets_lost'] }}
                                    <span class="ml-1 text-xs {{ $setDiff > 0 ? 'text-success' : ($setDiff < 0 ? 'text-danger' : 'text-text-muted') }}">
                                        ({{ $setDiff > 0 ? '+' : '' }}{{ $setDiff }})
                                    </span>
                                </span>
                            </td>
                            <td class="whitespace-nowrap py-2 text-center sm:py-3">
                                <span class="text-xs {{ $gameDiff > 0 ? 'text-success' : ($gameDiff < 0 ? 'text-danger' : 'text-text-secondary') }} sm:hidden">{{ $gameDiff > 0 ? '+' : '' }}{{ $gameDiff }}</span>
                                <span class="hidden text-text-secondary sm:inline">
                                    {{ $standing['games_won'] }}-{{ $standing['games_lost'] }}
                                    <span class="ml-1 text-xs {{ $gameDiff > 0 ? 'text-success' : ($gameDiff < 0 ? 'text-danger' : 'text-text-muted') }}">
                                        ({{ $gameDiff > 0 ? '+' : '' }}{{ $gameDiff }})
                                    </span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-text-muted">{{ __('messages.no_matches_completed') }}</p>
    @endif
</div>
