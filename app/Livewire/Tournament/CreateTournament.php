<?php

namespace App\Livewire\Tournament;

use App\Models\Tournament;
use Livewire\Component;

class CreateTournament extends Component
{
    public string $tournamentName = '';

    public string $startDate = '';

    public string $endDate = '';

    public string $tournamentFormat = 'round_robin';

    public bool $hasDoubles = false;

    public string $doublesFormat = 'round_robin';

    public function mount(): void
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(2)->format('Y-m-d');
    }

    public function createTournament(): void
    {
        $validated = $this->validate([
            'tournamentName' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'tournamentFormat' => 'required|in:round_robin,round_robin_finals',
            'hasDoubles' => 'boolean',
            'doublesFormat' => 'required|in:round_robin,round_robin_finals',
        ]);

        $tournament = Tournament::create([
            'name' => $validated['tournamentName'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'format' => $validated['tournamentFormat'],
            'has_doubles' => $validated['hasDoubles'],
            'doubles_format' => $validated['doublesFormat'],
        ]);

        $this->redirect(route('home', $tournament), navigate: true);
    }

    public function render()
    {
        return view('livewire.tournament.create-tournament');
    }
}
