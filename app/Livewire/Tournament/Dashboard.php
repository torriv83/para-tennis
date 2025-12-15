<?php

namespace App\Livewire\Tournament;

use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\PredictionService;
use App\Services\StandingsService;
use App\TournamentFormat;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Tournament $tournament = null;

    public string $tournamentName = '';

    public string $startDate = '';

    public string $endDate = '';

    public string $tournamentFormat = 'round_robin';

    public bool $hasDoubles = false;

    public string $newPlayerName = '';

    public string $activeTab = 'overview';

    public bool $showPlayersDrawer = false;

    public array $selectedPlayers = [];

    public bool $editingTournament = false;

    public string $editName = '';

    public string $editStartDate = '';

    public string $editEndDate = '';

    public string $editFormat = '';

    public bool $editHasDoubles = false;

    public bool $showDoublesForm = false;

    public ?int $doublesTeam1Player1 = null;

    public ?int $doublesTeam1Player2 = null;

    public ?int $doublesTeam2Player1 = null;

    public ?int $doublesTeam2Player2 = null;

    public function mount(?Tournament $tournament = null): void
    {
        if ($tournament?->id) {
            $this->tournament = $tournament->load([
                'players',
                'games.player1',
                'games.player2',
                'games.player1Partner',
                'games.player2Partner',
            ]);
        }

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
        ]);

        $tournament = Tournament::create([
            'name' => $validated['tournamentName'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'format' => $validated['tournamentFormat'],
            'has_doubles' => $validated['hasDoubles'],
        ]);

        $this->redirect(route('home', $tournament), navigate: true);
    }

    public function addPlayer(): void
    {
        $this->validate([
            'newPlayerName' => 'required|string|max:255',
        ]);

        // Check if player already exists
        $existingPlayer = $this->tournament->players()->where('name', $this->newPlayerName)->first();
        if (! $existingPlayer) {
            $this->tournament->players()->create([
                'name' => $this->newPlayerName,
            ]);
        }

        $this->reset('newPlayerName');
        $this->tournament->load('players');
    }

    public function removePlayer(int $playerId): void
    {
        Player::where('id', $playerId)
            ->where('tournament_id', $this->tournament->id)
            ->delete();

        $this->tournament->load('players');
    }

    public function generateSchedule(): void
    {
        if ($this->tournament->players->count() < 2) {
            return;
        }

        $this->tournament->games()->delete();

        $players = $this->tournament->players;

        foreach ($players as $i => $player1) {
            foreach ($players as $j => $player2) {
                if ($i < $j) {
                    $this->tournament->games()->create([
                        'player1_id' => $player1->id,
                        'player2_id' => $player2->id,
                    ]);
                }
            }
        }

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    public function updateGameResult(int $gameId, int $p1Sets, int $p2Sets, int $p1Games, int $p2Games, ?array $setScores = null): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game) {
            return;
        }

        // Best of 3: require a winner (2 sets) before marking complete
        $hasWinner = ($p1Sets === 2 || $p2Sets === 2);

        if (! $hasWinner) {
            return;
        }

        $game->update([
            'player1_sets' => $p1Sets,
            'player2_sets' => $p2Sets,
            'player1_games' => $p1Games,
            'player2_games' => $p2Games,
            'set_scores' => $setScores,
            'completed' => true,
        ]);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);

        // Auto-create final match when round-robin completes for round_robin_finals format
        if (! $game->is_final) {
            $this->maybeCreateFinalMatch();
        }
    }

    public function swapPlayers(int $gameId): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game) {
            return;
        }

        // Swap set scores (reverse each [p1, p2] pair to [p2, p1])
        $swappedSetScores = null;
        if ($game->set_scores) {
            $swappedSetScores = array_map(fn ($set) => [$set[1], $set[0]], $game->set_scores);
        }

        $updateData = [
            'player1_id' => $game->player2_id,
            'player2_id' => $game->player1_id,
            'player1_sets' => $game->player2_sets,
            'player2_sets' => $game->player1_sets,
            'player1_games' => $game->player2_games,
            'player2_games' => $game->player1_games,
            'set_scores' => $swappedSetScores,
        ];

        // Swap partners for doubles matches
        if ($game->is_doubles) {
            $updateData['player1_partner_id'] = $game->player2_partner_id;
            $updateData['player2_partner_id'] = $game->player1_partner_id;
        }

        $game->update($updateData);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    public function deleteTournament(): void
    {
        $this->tournament->delete();
        $this->tournament = null;
    }

    public function startEditingTournament(): void
    {
        $this->editName = $this->tournament->name;
        $this->editStartDate = $this->tournament->start_date->format('Y-m-d');
        $this->editEndDate = $this->tournament->end_date?->format('Y-m-d') ?? '';
        $this->editFormat = $this->tournament->format->value;
        $this->editHasDoubles = $this->tournament->has_doubles;
        $this->editingTournament = true;
    }

    public function updateTournament(): void
    {
        $validated = $this->validate([
            'editName' => 'required|string|max:255',
            'editStartDate' => 'required|date',
            'editEndDate' => 'required|date|after_or_equal:editStartDate',
            'editFormat' => 'required|in:round_robin,round_robin_finals',
            'editHasDoubles' => 'boolean',
        ]);

        $this->tournament->update([
            'name' => $validated['editName'],
            'start_date' => $validated['editStartDate'],
            'end_date' => $validated['editEndDate'],
            'format' => $validated['editFormat'],
            'has_doubles' => $validated['editHasDoubles'],
        ]);

        $this->editingTournament = false;
    }

    public function cancelEditingTournament(): void
    {
        $this->editingTournament = false;
        $this->reset(['editName', 'editStartDate', 'editEndDate', 'editFormat', 'editHasDoubles']);
    }

    public function newTournament(): void
    {
        $this->redirect(route('home'), navigate: true);
    }

    public function selectTournament(int $tournamentId): void
    {
        $tournament = Tournament::find($tournamentId);
        if ($tournament) {
            $this->redirect(route('home', $tournament), navigate: true);
        }
    }

    public function addPlayerAndUpdateSchedule(): void
    {
        $this->validate([
            'newPlayerName' => 'required|string|max:255',
        ]);

        // Check if player already exists
        $existingPlayer = $this->tournament->players()->where('name', $this->newPlayerName)->first();
        if ($existingPlayer) {
            $this->reset('newPlayerName');

            return;
        }

        $newPlayer = $this->tournament->players()->create([
            'name' => $this->newPlayerName,
        ]);

        // Add games for the new player against all existing players
        foreach ($this->tournament->players as $existingPlayer) {
            if ($existingPlayer->id !== $newPlayer->id) {
                $this->tournament->games()->create([
                    'player1_id' => $existingPlayer->id,
                    'player2_id' => $newPlayer->id,
                ]);
            }
        }

        $this->reset('newPlayerName');
        $this->tournament->load(['players', 'games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    public function updateGameSchedule(int $gameId, ?string $scheduledAt): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game) {
            return;
        }

        $game->update([
            'scheduled_at' => $scheduledAt ?: null,
        ]);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    public function recordWalkover(int $gameId, int $winnerId): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game) {
            return;
        }

        // Verify winner is one of the players in this game
        if ($winnerId !== $game->player1_id && $winnerId !== $game->player2_id) {
            return;
        }

        $game->update([
            'is_walkover' => true,
            'walkover_winner_id' => $winnerId,
            'player1_sets' => 0,
            'player2_sets' => 0,
            'player1_games' => 0,
            'player2_games' => 0,
            'completed' => true,
        ]);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);

        // Auto-create final match when round-robin completes for round_robin_finals format
        if (! $game->is_final) {
            $this->maybeCreateFinalMatch();
        }
    }

    public function clearWalkover(int $gameId): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game || ! $game->is_walkover) {
            return;
        }

        $game->update([
            'is_walkover' => false,
            'walkover_winner_id' => null,
            'completed' => false,
        ]);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    public function togglePlayersDrawer(): void
    {
        $this->showPlayersDrawer = ! $this->showPlayersDrawer;
        $this->selectedPlayers = [];
    }

    public function importPlayers(): void
    {
        if (empty($this->selectedPlayers)) {
            return;
        }

        $hasGames = $this->tournament->games->isNotEmpty();

        foreach ($this->selectedPlayers as $name) {
            $existingPlayer = $this->tournament->players()->where('name', $name)->first();
            if ($existingPlayer) {
                continue;
            }

            $newPlayer = $this->tournament->players()->create(['name' => $name]);

            if ($hasGames) {
                foreach ($this->tournament->players as $existingPlayer) {
                    if ($existingPlayer->id !== $newPlayer->id) {
                        $this->tournament->games()->create([
                            'player1_id' => $existingPlayer->id,
                            'player2_id' => $newPlayer->id,
                        ]);
                    }
                }
            }
        }

        $this->selectedPlayers = [];
        $this->tournament->load([
            'players',
            'games.player1',
            'games.player2',
            'games.player1Partner',
            'games.player2Partner',
        ]);
    }

    public function createDoublesMatch(): void
    {
        if (! $this->tournament?->has_doubles) {
            return;
        }

        $playerIds = [
            $this->doublesTeam1Player1,
            $this->doublesTeam1Player2,
            $this->doublesTeam2Player1,
            $this->doublesTeam2Player2,
        ];

        // Validate all players are selected and distinct
        if (in_array(null, $playerIds, true) || count(array_unique($playerIds)) !== 4) {
            return;
        }

        // Validate all players belong to this tournament
        $validPlayers = $this->tournament->players()
            ->whereIn('id', $playerIds)
            ->count();

        if ($validPlayers !== 4) {
            return;
        }

        $this->tournament->games()->create([
            'player1_id' => $this->doublesTeam1Player1,
            'player1_partner_id' => $this->doublesTeam1Player2,
            'player2_id' => $this->doublesTeam2Player1,
            'player2_partner_id' => $this->doublesTeam2Player2,
            'is_doubles' => true,
        ]);

        $this->resetDoublesForm();
        $this->tournament->load([
            'games.player1',
            'games.player2',
            'games.player1Partner',
            'games.player2Partner',
        ]);
    }

    public function resetDoublesForm(): void
    {
        $this->showDoublesForm = false;
        $this->doublesTeam1Player1 = null;
        $this->doublesTeam1Player2 = null;
        $this->doublesTeam2Player1 = null;
        $this->doublesTeam2Player2 = null;
    }

    public function maybeCreateFinalMatch(): void
    {
        // Only for round_robin_finals format
        if ($this->tournament->format !== TournamentFormat::RoundRobinFinals) {
            return;
        }

        // Check if final already exists
        if ($this->tournament->games->where('is_final', true)->isNotEmpty()) {
            return;
        }

        // Check if all round-robin games are completed (excluding doubles)
        $roundRobinGames = $this->tournament->games
            ->where('is_final', false)
            ->where('is_doubles', false);

        if ($roundRobinGames->isEmpty()) {
            return;
        }

        $allCompleted = $roundRobinGames->every(fn ($game) => $game->completed);
        if (! $allCompleted) {
            return;
        }

        // Get top 2 players from standings
        $standings = $this->standings;
        if (count($standings) < 2) {
            return;
        }

        $topTwo = array_slice($standings, 0, 2);
        $player1 = $topTwo[0]['player'];
        $player2 = $topTwo[1]['player'];

        // Create final match
        $this->tournament->games()->create([
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'is_final' => true,
        ]);

        $this->tournament->load(['games.player1', 'games.player2', 'games.player1Partner', 'games.player2Partner']);
    }

    #[Computed]
    public function finalMatch(): ?Game
    {
        if (! $this->tournament) {
            return null;
        }

        return $this->tournament->games->where('is_final', true)->first();
    }

    #[Computed]
    public function doublesMatch(): ?Game
    {
        if (! $this->tournament || ! $this->tournament->has_doubles) {
            return null;
        }

        return $this->tournament->games->where('is_doubles', true)->first();
    }

    #[Computed]
    public function roundRobinComplete(): bool
    {
        if (! $this->tournament) {
            return false;
        }

        $roundRobinGames = $this->tournament->games
            ->where('is_final', false)
            ->where('is_doubles', false);

        if ($roundRobinGames->isEmpty()) {
            return false;
        }

        return $roundRobinGames->every(fn ($game) => $game->completed);
    }

    #[Computed]
    public function tournamentChampion(): ?Player
    {
        // For round_robin_finals format, champion is determined by final match
        if ($this->tournament?->format === TournamentFormat::RoundRobinFinals) {
            $final = $this->finalMatch;
            if (! $final || ! $final->completed) {
                return null;
            }

            if ($final->is_walkover) {
                return Player::find($final->walkover_winner_id);
            }

            if ($final->player1_sets > $final->player2_sets) {
                return $final->player1;
            }

            return $final->player2;
        }

        // For round_robin format, champion is the top player when all games are complete
        if ($this->tournament?->format === TournamentFormat::RoundRobin && $this->roundRobinComplete) {
            $standings = $this->standings;
            if (! empty($standings)) {
                return $standings[0]['player'];
            }
        }

        return null;
    }

    #[Computed]
    public function playerHistory(): \Illuminate\Support\Collection
    {
        $currentPlayerNames = $this->tournament?->players->pluck('name')->toArray() ?? [];

        return Player::query()
            ->select('name')
            ->selectRaw('COUNT(*) as tournament_count')
            ->when($this->tournament, fn ($q) => $q->where('tournament_id', '!=', $this->tournament->id))
            ->groupBy('name')
            ->orderByDesc('tournament_count')
            ->get()
            ->filter(fn ($p) => ! in_array($p->name, $currentPlayerNames));
    }

    #[Computed]
    public function nextUp(): array
    {
        if (! $this->tournament || $this->tournament->games->isEmpty()) {
            return ['type' => 'none', 'games' => collect()];
        }

        $now = now();
        $today = $now->toDateString();
        $tomorrow = $now->copy()->addDay()->toDateString();

        $incompleteGames = $this->tournament->games
            ->where('completed', false)
            ->filter(fn ($g) => $g->scheduled_at !== null)
            ->sortBy('scheduled_at');

        $todayGames = $incompleteGames->filter(
            fn ($g) => $g->scheduled_at->toDateString() === $today && $g->scheduled_at->gte($now)
        );

        if ($todayGames->isNotEmpty()) {
            return [
                'type' => 'today',
                'label' => 'Next Up Today',
                'games' => $todayGames->take(1)->values(),
            ];
        }

        $tomorrowGames = $incompleteGames->filter(
            fn ($g) => $g->scheduled_at->toDateString() === $tomorrow
        );

        if ($tomorrowGames->isNotEmpty()) {
            return [
                'type' => 'tomorrow',
                'label' => "Tomorrow's Matches",
                'games' => $tomorrowGames->take(2)->values(),
            ];
        }

        return ['type' => 'none', 'games' => collect()];
    }

    #[Computed]
    public function standings(): array
    {
        if (! $this->tournament) {
            return [];
        }

        return (new StandingsService($this->tournament))->calculate();
    }

    #[Computed]
    public function formats(): array
    {
        return array_map(
            fn (TournamentFormat $format) => [
                'value' => $format->value,
                'label' => $format->label(),
            ],
            TournamentFormat::cases()
        );
    }

    #[Computed]
    public function allTournaments(): \Illuminate\Database\Eloquent\Collection
    {
        return Tournament::withCount('players')->orderByDesc('start_date')->get();
    }

    #[Computed]
    public function predictions(): array
    {
        if (! $this->tournament || $this->tournament->games->isEmpty()) {
            return [];
        }

        $service = new PredictionService($this->tournament);

        return $service->getPredictions();
    }

    public function render()
    {
        return view('livewire.tournament.dashboard');
    }
}
