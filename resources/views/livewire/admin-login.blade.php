<div>
    {{-- Admin/Logout Button --}}
    @guest
        <button
            wire:click="openModal"
            class="cursor-pointer rounded-lg border border-white/10 bg-surface-light px-4 py-2 text-sm font-medium text-text-primary transition hover:border-white/20 hover:bg-surface"
        >
            Admin
        </button>
    @endguest

    @auth
        <button
            wire:click="logout"
            class="cursor-pointer rounded-lg border border-white/10 bg-surface-light px-4 py-2 text-sm font-medium text-text-primary transition hover:border-white/20 hover:bg-surface"
        >
            Logg ut
        </button>
    @endauth

    {{-- Admin Login Modal --}}
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
                <div class="relative w-full max-w-md rounded-2xl border border-white/10 bg-surface p-6 shadow-xl">
                    <h3 class="mb-6 text-xl font-semibold">Admin Innlogging</h3>

                    <form wire:submit.prevent="login" class="space-y-5">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-text-secondary">E-post</label>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="admin@example.com"
                            >
                            @error('email') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-text-secondary">Passord</label>
                            <input
                                type="password"
                                id="password"
                                wire:model="password"
                                class="w-full rounded-lg border border-white/10 bg-background/50 px-4 py-3 text-text-primary placeholder-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="••••••••"
                            >
                            @error('password') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div>
                            <label class="flex cursor-pointer items-center gap-3">
                                <input
                                    type="checkbox"
                                    wire:model="remember"
                                    class="h-5 w-5 cursor-pointer rounded border-white/20 bg-background/50 text-primary focus:ring-2 focus:ring-primary/20"
                                >
                                <span class="text-sm text-text-primary">Husk meg</span>
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="cursor-pointer rounded-lg px-4 py-2 text-text-secondary transition hover:bg-surface-light hover:text-text-primary"
                            >
                                Avbryt
                            </button>
                            <button
                                type="submit"
                                class="cursor-pointer rounded-lg bg-primary px-6 py-2 font-medium text-white transition hover:bg-primary-hover"
                            >
                                Logg inn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
</div>
