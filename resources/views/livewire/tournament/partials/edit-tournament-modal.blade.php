{{-- Edit Tournament Modal --}}
@if($editingTournament)
    <template x-teleport="body">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="$wire.cancelEditingTournament()"
        >
            {{-- Backdrop --}}
            <div
                wire:click="cancelEditingTournament"
                class="fixed inset-0 bg-black/50"
            ></div>

            {{-- Modal Panel --}}
            <div class="relative max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl border border-white/10 bg-surface p-4 shadow-xl sm:p-6">
                <h3 class="mb-4 text-lg font-semibold sm:mb-6 sm:text-xl">{{ __('messages.edit_tournament') }}</h3>

                <form wire:submit="updateTournament" class="space-y-4 sm:space-y-5">
                    {{-- Tournament Name --}}
                    <div>
                        <label for="editName" class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_name') }}</label>
                        <input
                            type="text"
                            id="editName"
                            wire:model="editName"
                            class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            placeholder="{{ __('messages.tournament_name') }}"
                        >
                        @error('editName') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_dates') }}</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                            <div class="flex-1">
                                <input
                                    type="date"
                                    wire:model="editStartDate"
                                    class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>
                            <span class="hidden text-text-muted sm:block">{{ __('messages.to') }}</span>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    wire:model="editEndDate"
                                    class="w-full cursor-pointer rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                >
                            </div>
                        </div>
                        @error('editStartDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                        @error('editEndDate') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- Format Selection --}}
                    <div>
                        <label class="mb-3 block text-sm font-medium text-text-secondary">{{ __('messages.tournament_format') }}</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="group cursor-pointer">
                                <input type="radio" wire:model="editFormat" value="round_robin" class="peer hidden">
                                <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ __('messages.round_robin') }}</span>
                                    </div>
                                    <p class="text-sm text-text-muted">{{ __('messages.round_robin_short') }}</p>
                                </div>
                            </label>

                            <label class="group cursor-pointer">
                                <input type="radio" wire:model="editFormat" value="round_robin_finals" class="peer hidden">
                                <div class="rounded-xl border border-white/10 bg-background/30 p-4 transition peer-checked:border-primary peer-checked:bg-primary/10 group-hover:border-white/20">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-light">
                                            <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ __('messages.round_robin_finals') }}</span>
                                    </div>
                                    <p class="text-sm text-text-muted">{{ __('messages.round_robin_finals_short') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Include Doubles --}}
                    <div>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input
                                type="checkbox"
                                wire:model="editHasDoubles"
                                class="h-5 w-5 cursor-pointer rounded border-white/20 bg-background/50 text-primary focus:ring-primary/20"
                            >
                            <div>
                                <span class="font-medium text-text-primary">{{ __('messages.include_doubles') }}</span>
                                <p class="text-sm text-text-muted">{{ __('messages.include_doubles_desc') }}</p>
                            </div>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="cancelEditingTournament"
                            class="cursor-pointer rounded-lg px-4 py-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                        >
                            {{ __('messages.cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="cursor-pointer rounded-lg bg-primary px-6 py-2 font-medium text-white transition hover:bg-primary-hover"
                        >
                            {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
@endif
