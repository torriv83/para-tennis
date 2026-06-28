{{-- Tab Bar --}}
<div class="flex gap-1 rounded-lg border border-white/10 bg-surface p-1">
    <button
        wire:click="$set('activeTab', 'overview')"
        class="flex-1 cursor-pointer rounded-md px-2 py-2 text-xs font-medium transition sm:px-4 sm:text-sm {{ $activeTab === 'overview' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
    >
        {{ __('messages.overview') }}
    </button>
    <button
        wire:click="$set('activeTab', 'matches')"
        class="flex-1 cursor-pointer rounded-md px-2 py-2 text-xs font-medium transition sm:px-4 sm:text-sm {{ $activeTab === 'matches' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
    >
        {{ __('messages.matches') }}
    </button>
    @if($tournament->has_doubles)
        <button
            wire:click="$set('activeTab', 'doubles')"
            class="flex-1 cursor-pointer rounded-md px-2 py-2 text-xs font-medium transition sm:px-4 sm:text-sm {{ $activeTab === 'doubles' ? 'bg-primary text-white' : 'text-text-secondary hover:bg-surface-light hover:text-text-primary' }}"
        >
            {{ __('messages.doubles') }}
        </button>
    @endif
</div>
