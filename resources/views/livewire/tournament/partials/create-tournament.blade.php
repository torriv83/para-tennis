{{-- Hero Section for creating a new tournament --}}
<div class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-surface via-surface to-surface-light p-5 sm:p-8 md:p-12">
    {{-- Decorative elements --}}
    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>

    <div class="relative">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-sm text-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            {{ __('messages.new_tournament') }}
        </div>

        <h2 class="mb-3 text-2xl font-semibold sm:text-3xl md:text-4xl">{{ __('messages.create_your_tournament') }}</h2>
        <p class="mb-6 max-w-xl text-sm text-text-secondary sm:mb-8 sm:text-base">
            {{ __('messages.create_tournament_description') }}
        </p>

        <form wire:submit="createTournament" class="space-y-6">
            {{-- Tournament Name --}}
            <div>
                <label for="tournamentName" class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_name') }}</label>
                <input
                    type="text"
                    id="tournamentName"
                    wire:model="tournamentName"
                    class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-lg text-text-primary placeholder-text-muted backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    placeholder="{{ __('messages.tournament_name_placeholder') }}"
                >
                @error('tournamentName') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Date Range --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_dates') }}</label>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                    <div class="flex-1">
                        <input
                            type="date"
                            wire:model="startDate"
                            class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                        >
                    </div>
                    <span class="hidden text-text-muted sm:block">{{ __('messages.to') }}</span>
                    <div class="flex-1">
                        <input
                            type="date"
                            wire:model="endDate"
                            class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary backdrop-blur-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                        >
                    </div>
                </div>
                @error('startDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                @error('endDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Format Selection --}}
            <div>
                <label class="mb-3 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_format') }}</label>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="group cursor-pointer">
                        <input type="radio" wire:model="tournamentFormat" value="round_robin" class="peer hidden">
                        <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                            <div class="mb-2 flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('messages.round_robin') }}</span>
                            </div>
                            <p class="text-sm text-text-muted">{{ __('messages.round_robin_desc') }}</p>
                        </div>
                    </label>

                    <label class="group cursor-pointer">
                        <input type="radio" wire:model="tournamentFormat" value="round_robin_finals" class="peer hidden">
                        <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                            <div class="mb-2 flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                    <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('messages.round_robin_finals') }}</span>
                            </div>
                            <p class="text-sm text-text-muted">{{ __('messages.round_robin_finals_desc') }}</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Include Doubles --}}
            <div>
                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="hasDoubles"
                        class="h-5 w-5 cursor-pointer rounded border-white/20 bg-background/50 text-primary focus:ring-2 focus:ring-primary/20"
                    >
                    <div>
                        <span class="font-medium">{{ __('messages.include_doubles') }}</span>
                        <p class="text-sm text-text-muted">{{ __('messages.include_doubles_desc') }}</p>
                    </div>
                </label>
            </div>

            <button
                type="submit"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('messages.create_tournament') }}
            </button>
        </form>
    </div>
</div>

{{-- Tournament History --}}
@if($this->pastTournaments->count() > 0)
    <div class="rounded-xl border border-white/10 bg-surface p-4 sm:p-6">
        <h3 class="mb-4 text-lg font-medium">{{ __('messages.previous_tournaments') }}</h3>
        <div class="space-y-2">
            @foreach($this->pastTournaments as $t)
                <button
                    wire:key="history-{{ $t->id }}"
                    wire:click="selectTournament({{ $t->id }})"
                    class="flex w-full cursor-pointer flex-col gap-1 rounded-lg border border-white/5 bg-surface-light px-4 py-3 text-left transition hover:border-white/20 sm:flex-row sm:items-center sm:justify-between sm:gap-2"
                >
                    <div class="min-w-0 flex-1">
                        <span class="font-medium">{{ $t->name }}</span>
                        <span class="mt-1 block text-sm text-text-muted sm:ml-2 sm:mt-0 sm:inline">
                            {{ localized_date_range($t->start_date, $t->end_date) }}
                        </span>
                    </div>
                    <span class="text-sm text-text-muted">
                        {{ $t->players_count }} {{ __('messages.players') }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>
@endif
