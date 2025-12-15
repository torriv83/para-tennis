{{-- Tab Bar --}}
<div class="flex gap-1 rounded-lg border border-white/10 bg-surface p-1">
    <button
        wire:click="$set('activeTab', 'overview')"
        class="flex-1 cursor-pointer rounded-md px-4 py-2 text-sm font-medium transition {{ $activeTab === 'overview' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
    >
        {{ __('messages.overview') }}
    </button>
    <button
        wire:click="$set('activeTab', 'matches')"
        class="flex-1 cursor-pointer rounded-md px-4 py-2 text-sm font-medium transition {{ $activeTab === 'matches' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
    >
        {{ __('messages.matches') }}
    </button>
</div>
