<div>
    {{-- Only show for guests when tournament has PIN enabled --}}
    @guest
        @if($hasPinEnabled)
            @if($hasAccess)
                {{-- User has PIN access - show logout button --}}
                <button
                    wire:click="logout"
                    class="flex cursor-pointer items-center gap-2 rounded-lg border border-primary/30 bg-primary/10 px-3 py-2 text-sm font-medium text-primary transition hover:border-primary/50 hover:bg-primary/20"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('messages.pin_access') }}</span>
                    <svg class="h-3 w-3 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            @else
                {{-- No access yet - show PIN login button --}}
                <button
                    wire:click="openModal"
                    class="flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 bg-surface-light px-3 py-2 text-sm font-medium text-text-primary transition hover:border-white/20 hover:bg-surface"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('messages.enter_pin') }}</span>
                </button>
            @endif
        @endif
    @endguest

    {{-- PIN Login Modal --}}
    @if($showModal)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @keydown.escape.window="$wire.closeModal()"
            >
                {{-- Backdrop --}}
                <div
                    wire:click="closeModal"
                    class="fixed inset-0 bg-black/50"
                ></div>

                {{-- Modal Panel --}}
                <div class="relative w-full max-w-sm rounded-2xl border border-white/10 bg-surface p-6 shadow-xl">
                    <div class="mb-6 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold">{{ __('messages.enter_pin') }}</h3>
                        <p class="mt-1 text-sm text-text-muted">{{ __('messages.enter_pin_description') }}</p>
                    </div>

                    <form wire:submit.prevent="verifyPin" class="space-y-5">
                        {{-- PIN Input --}}
                        <div>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="6"
                                wire:model="pin"
                                class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-4 text-center font-mono text-2xl tracking-[0.5em] text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="{{ __('messages.pin_placeholder') }}"
                                autofocus
                            >
                            @error('pin') <span class="mt-2 block text-center text-sm text-danger">{{ $message }}</span> @enderror
                            @if($error)
                                <span class="mt-2 block text-center text-sm text-danger">{{ $error }}</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-center gap-3 pt-2">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="cursor-pointer rounded-lg px-4 py-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                            >
                                {{ __('messages.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="cursor-pointer rounded-lg bg-primary px-6 py-2 font-medium text-white transition hover:bg-primary-hover"
                            >
                                {{ __('messages.verify_pin') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
</div>
