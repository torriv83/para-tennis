<?php

namespace App\Livewire\Tournament;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\PredictionService;
use App\Services\StandingsService;
use App\TournamentFormat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Tournament $tournament = null;

    public string $newPlayerName = '';

    public bool $newPlayerPlaysSingles = true;

    public bool $newPlayerPlaysDoubles = false;

    public string $activeTab = 'overview';

    public bool $showPlayersDrawer = false;

    public array $selectedPlayers = [];

    public bool $editingTournament = false;

    public string $editName = '';

    public string $editStartDate = '';

    public string $editEndDate = '';

    public string $editFormat = '';

    public bool $editHasDoubles = false;

    public string $editDoublesFormat = 'round_robin';

    public bool $showTeamForm = false;

    public ?int $newTeamPlayer1 = null;

    public ?int $newTeamPlayer2 = null;

    public function mount(?Tournament $tournament = null): void
    {
        if ($tournament?->id) {
            $this->tournament = $tournament->load($this->tournamentRelations());
        } else {
            // Redirect to active tournament if one exists
            $activeTournament = $this->findActiveTournament();
            if ($activeTournament) {
                $this->redirect(route('home', $activeTournament), navigate: true);
            }
        }
    }

    /**
     * @return list<string>
     */
    protected function tournamentRelations(): array
    {
        return [
            'players',
            'teams.player1',
            'teams.player2',
            'games.player1',
            'games.player2',
            'games.team1.player1',
            'games.team1.player2',
            'games.team2.player1',
            'games.team2.player2',
            'games.walkoverWinnerTeam.player1',
            'games.walkoverWinnerTeam.player2',
        ];
    }

    protected function reloadTournament(): void
    {
        $this->tournament->load($this->tournamentRelations());
    }

    protected function findActiveTournament(): ?Tournament
    {
        $today = now()->startOfDay();

        return Tournament::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();
    }

    public function addPlayer(): void
    {
        $this->validate([
            'newPlayerName' => 'required|string|max:255',
        ]);

        $existingPlayer = $this->tournament->players()->where('name', $this->newPlayerName)->first();
        if (! $existingPlayer) {
            $this->tournament->players()->create([
                'name' => $this->newPlayerName,
                'plays_singles' => $this->newPlayerPlaysSingles,
                'plays_doubles' => $this->newPlayerPlaysDoubles,
            ]);
        }

        $this->reset('newPlayerName');
        $this->reloadTournament();
    }

    public function removePlayer(int $playerId): void
    {
        $player = $this->tournament->players()->whereKey($playerId)->first();
        if (! $player) {
            return;
        }

        // Disband any doubles team (and its games) the player belongs to first,
        // otherwise the team cascade would leave doubles games with null teams.
        foreach ($this->tournament->teams as $team) {
            if ($team->hasPlayer($playerId)) {
                $this->deleteTeam($team);
            }
        }

        $player->delete();

        $this->reloadTournament();
    }

    public function setPlayerParticipation(int $playerId, bool $playsSingles, bool $playsDoubles): void
    {
        if (! Auth::check()) {
            return;
        }

        $player = $this->tournament->players()->whereKey($playerId)->first();
        if (! $player) {
            return;
        }

        $player->update([
            'plays_singles' => $playsSingles,
            'plays_doubles' => $playsDoubles,
        ]);

        // Removing a player from doubles disbands any team they belong to.
        if (! $playsDoubles) {
            $teams = $this->tournament->teams()
                ->where(function ($query) use ($playerId) {
                    $query->where('player1_id', $playerId)
                        ->orWhere('player2_id', $playerId);
                })
                ->get();

            foreach ($teams as $team) {
                $this->deleteTeam($team);
            }
        }

        $this->reloadTournament();
    }

    public function generateSchedule(): void
    {
        $singlesPlayers = $this->tournament->players->where('plays_singles', true)->values();

        if ($singlesPlayers->count() < 2) {
            return;
        }

        $this->tournament->games()->where('is_doubles', false)->delete();

        foreach ($singlesPlayers as $i => $player1) {
            foreach ($singlesPlayers as $j => $player2) {
                if ($i < $j) {
                    $this->tournament->games()->create([
                        'player1_id' => $player1->id,
                        'player2_id' => $player2->id,
                    ]);
                }
            }
        }

        $this->reloadTournament();
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

        $this->reloadTournament();

        // Auto-create the relevant final match when its round-robin completes.
        if (! $game->is_final) {
            $this->maybeCreateFinalMatch($game->is_doubles);
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
            'player1_sets' => $game->player2_sets,
            'player2_sets' => $game->player1_sets,
            'player1_games' => $game->player2_games,
            'player2_games' => $game->player1_games,
            'set_scores' => $swappedSetScores,
        ];

        if ($game->is_doubles) {
            $updateData['team1_id'] = $game->team2_id;
            $updateData['team2_id'] = $game->team1_id;
        } else {
            $updateData['player1_id'] = $game->player2_id;
            $updateData['player2_id'] = $game->player1_id;
        }

        $game->update($updateData);

        $this->reloadTournament();
    }

    public function deleteTournament(): void
    {
        $this->tournament->delete();
        $this->tournament = null;
    }

    public function generatePin(): void
    {
        $pin = $this->tournament->generatePin();
        $this->dispatch('pin-generated', pin: $pin);
    }

    public function clearPin(): void
    {
        $this->tournament->clearPin();
    }

    public function startEditingTournament(): void
    {
        $this->editName = $this->tournament->name;
        $this->editStartDate = $this->tournament->start_date->format('Y-m-d');
        $this->editEndDate = $this->tournament->end_date?->format('Y-m-d') ?? '';
        $this->editFormat = $this->tournament->format->value;
        $this->editHasDoubles = $this->tournament->has_doubles;
        $this->editDoublesFormat = $this->tournament->doubles_format?->value ?? 'round_robin';
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
            'editDoublesFormat' => 'required|in:round_robin,round_robin_finals',
        ]);

        $this->tournament->update([
            'name' => $validated['editName'],
            'start_date' => $validated['editStartDate'],
            'end_date' => $validated['editEndDate'],
            'format' => $validated['editFormat'],
            'has_doubles' => $validated['editHasDoubles'],
            'doubles_format' => $validated['editDoublesFormat'],
        ]);

        $this->editingTournament = false;
    }

    public function cancelEditingTournament(): void
    {
        $this->editingTournament = false;
        $this->reset(['editName', 'editStartDate', 'editEndDate', 'editFormat', 'editHasDoubles', 'editDoublesFormat']);
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

        $existingPlayer = $this->tournament->players()->where('name', $this->newPlayerName)->first();
        if ($existingPlayer) {
            $this->reset('newPlayerName');

            return;
        }

        $newPlayer = $this->tournament->players()->create([
            'name' => $this->newPlayerName,
            'plays_singles' => $this->newPlayerPlaysSingles,
            'plays_doubles' => $this->newPlayerPlaysDoubles,
        ]);

        // Add singles games for the new player against existing singles players.
        if ($newPlayer->plays_singles) {
            foreach ($this->tournament->players as $existingPlayer) {
                if ($existingPlayer->id !== $newPlayer->id && $existingPlayer->plays_singles) {
                    $this->tournament->games()->create([
                        'player1_id' => $existingPlayer->id,
                        'player2_id' => $newPlayer->id,
                    ]);
                }
            }
        }

        $this->reset('newPlayerName');
        $this->reloadTournament();
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

        $this->reloadTournament();
    }

    public function recordWalkover(int $gameId, int $winnerId): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game || $game->is_doubles) {
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

        $this->reloadTournament();

        if (! $game->is_final) {
            $this->maybeCreateFinalMatch(false);
        }
    }

    public function recordDoublesWalkover(int $gameId, int $winnerTeamId): void
    {
        $game = Game::where('id', $gameId)
            ->where('tournament_id', $this->tournament->id)
            ->first();

        if (! $game || ! $game->is_doubles) {
            return;
        }

        if ($winnerTeamId !== $game->team1_id && $winnerTeamId !== $game->team2_id) {
            return;
        }

        $game->update([
            'is_walkover' => true,
            'walkover_winner_team_id' => $winnerTeamId,
            'player1_sets' => 0,
            'player2_sets' => 0,
            'player1_games' => 0,
            'player2_games' => 0,
            'completed' => true,
        ]);

        $this->reloadTournament();

        if (! $game->is_final) {
            $this->maybeCreateFinalMatch(true);
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
            'walkover_winner_team_id' => null,
            'completed' => false,
        ]);

        $this->reloadTournament();
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

        $hasSinglesGames = $this->tournament->games->where('is_doubles', false)->isNotEmpty();

        // Snapshot the pre-import singles players before creating any new ones.
        $existingSinglesPlayers = $this->tournament->players->where('plays_singles', true)->values();

        $newPlayers = collect();
        foreach ($this->selectedPlayers as $name) {
            if ($this->tournament->players()->where('name', $name)->exists()) {
                continue;
            }

            $newPlayers->push($this->tournament->players()->create([
                'name' => $name,
                'plays_singles' => true,
                'plays_doubles' => false,
            ]));
        }

        // Wire imported players against the existing field AND against each other.
        if ($hasSinglesGames) {
            foreach ($newPlayers as $i => $newPlayer) {
                foreach ($existingSinglesPlayers as $existingPlayer) {
                    $this->tournament->games()->create([
                        'player1_id' => $existingPlayer->id,
                        'player2_id' => $newPlayer->id,
                    ]);
                }

                foreach ($newPlayers as $j => $otherNewPlayer) {
                    if ($i < $j) {
                        $this->tournament->games()->create([
                            'player1_id' => $newPlayer->id,
                            'player2_id' => $otherNewPlayer->id,
                        ]);
                    }
                }
            }
        }

        $this->selectedPlayers = [];
        $this->reloadTournament();
    }

    public function createTeam(): void
    {
        if (! Auth::check() || ! $this->tournament?->has_doubles) {
            return;
        }

        $playerIds = [$this->newTeamPlayer1, $this->newTeamPlayer2];

        if (in_array(null, $playerIds, true) || $this->newTeamPlayer1 === $this->newTeamPlayer2) {
            return;
        }

        // Both players must belong to this tournament and play doubles.
        $eligible = $this->tournament->players()
            ->whereIn('id', $playerIds)
            ->where('plays_doubles', true)
            ->count();

        if ($eligible !== 2) {
            return;
        }

        // Neither player may already belong to a team.
        $alreadyTeamed = $this->tournament->teams()
            ->where(function ($query) use ($playerIds) {
                $query->whereIn('player1_id', $playerIds)
                    ->orWhereIn('player2_id', $playerIds);
            })
            ->exists();

        if ($alreadyTeamed) {
            return;
        }

        $this->tournament->teams()->create([
            'player1_id' => $this->newTeamPlayer1,
            'player2_id' => $this->newTeamPlayer2,
        ]);

        $this->resetTeamForm();
        $this->reloadTournament();
    }

    public function removeTeam(int $teamId): void
    {
        if (! Auth::check()) {
            return;
        }

        $team = $this->tournament->teams()->whereKey($teamId)->first();

        if (! $team) {
            return;
        }

        $this->deleteTeam($team);
        $this->reloadTournament();
    }

    protected function deleteTeam(Team $team): void
    {
        $this->tournament->games()
            ->where('is_doubles', true)
            ->where(function ($query) use ($team) {
                $query->where('team1_id', $team->id)
                    ->orWhere('team2_id', $team->id)
                    ->orWhere('walkover_winner_team_id', $team->id);
            })
            ->delete();

        $team->delete();
    }

    public function resetTeamForm(): void
    {
        $this->showTeamForm = false;
        $this->newTeamPlayer1 = null;
        $this->newTeamPlayer2 = null;
    }

    public function generateDoublesSchedule(): void
    {
        if (! Auth::check()) {
            return;
        }

        $teams = $this->tournament->teams;

        if ($teams->count() < 2) {
            return;
        }

        $this->tournament->games()->where('is_doubles', true)->delete();

        $teamList = $teams->values();

        foreach ($teamList as $i => $team1) {
            foreach ($teamList as $j => $team2) {
                if ($i < $j) {
                    $this->tournament->games()->create([
                        'team1_id' => $team1->id,
                        'team2_id' => $team2->id,
                        'is_doubles' => true,
                    ]);
                }
            }
        }

        $this->reloadTournament();
    }

    protected function maybeCreateFinalMatch(bool $isDoubles): void
    {
        $format = $isDoubles ? $this->tournament->doubles_format : $this->tournament->format;
        if ($format !== TournamentFormat::RoundRobinFinals) {
            return;
        }

        // The final must not exist yet, and the round-robin must be complete.
        if ($this->finalMatchFor($isDoubles) || ! $this->isRoundRobinComplete($isDoubles)) {
            return;
        }

        $standings = $isDoubles ? $this->doublesStandings : $this->standings;
        if (count($standings) < 2) {
            return;
        }

        $topTwo = array_slice($standings, 0, 2);

        $this->tournament->games()->create($isDoubles
            ? [
                'team1_id' => $topTwo[0]['team']->id,
                'team2_id' => $topTwo[1]['team']->id,
                'is_doubles' => true,
                'is_final' => true,
            ]
            : [
                'player1_id' => $topTwo[0]['player']->id,
                'player2_id' => $topTwo[1]['player']->id,
                'is_final' => true,
            ]);

        $this->reloadTournament();
    }

    /**
     * Round-robin games for the given competition (excludes the final).
     *
     * @return \Illuminate\Support\Collection<int, Game>
     */
    protected function roundRobinGames(bool $isDoubles): \Illuminate\Support\Collection
    {
        return $this->tournament->games
            ->where('is_final', false)
            ->where('is_doubles', $isDoubles);
    }

    protected function isRoundRobinComplete(bool $isDoubles): bool
    {
        $games = $this->roundRobinGames($isDoubles);

        return $games->isNotEmpty() && $games->every(fn ($game) => $game->completed);
    }

    protected function finalMatchFor(bool $isDoubles): ?Game
    {
        return $this->tournament->games
            ->where('is_final', true)
            ->where('is_doubles', $isDoubles)
            ->first();
    }

    #[Computed]
    public function finalMatch(): ?Game
    {
        if (! $this->tournament) {
            return null;
        }

        return $this->finalMatchFor(false);
    }

    #[Computed]
    public function doublesFinalMatch(): ?Game
    {
        if (! $this->tournament || ! $this->tournament->has_doubles) {
            return null;
        }

        return $this->finalMatchFor(true);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Game>
     */
    #[Computed]
    public function doublesGames(): \Illuminate\Support\Collection
    {
        if (! $this->tournament) {
            return collect();
        }

        return $this->roundRobinGames(true)->sortBy('scheduled_at')->values();
    }

    #[Computed]
    public function roundRobinComplete(): bool
    {
        if (! $this->tournament) {
            return false;
        }

        return $this->isRoundRobinComplete(false);
    }

    #[Computed]
    public function doublesRoundRobinComplete(): bool
    {
        if (! $this->tournament) {
            return false;
        }

        return $this->isRoundRobinComplete(true);
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
    public function doublesChampion(): ?Team
    {
        if (! $this->tournament?->has_doubles) {
            return null;
        }

        if ($this->tournament->doubles_format === TournamentFormat::RoundRobinFinals) {
            $final = $this->doublesFinalMatch;
            if (! $final || ! $final->completed) {
                return null;
            }

            return $final->winningTeam();
        }

        if ($this->tournament->doubles_format === TournamentFormat::RoundRobin && $this->doublesRoundRobinComplete) {
            $standings = $this->doublesStandings;
            if (! empty($standings)) {
                return $standings[0]['team'];
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
            ->where('is_doubles', false)
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
    public function doublesStandings(): array
    {
        if (! $this->tournament) {
            return [];
        }

        return (new StandingsService($this->tournament))->calculateDoubles();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Player>
     */
    #[Computed]
    public function availableDoublesPlayers(): \Illuminate\Support\Collection
    {
        if (! $this->tournament) {
            return collect();
        }

        $teamedPlayerIds = $this->tournament->teams
            ->flatMap(fn (Team $team) => [$team->player1_id, $team->player2_id])
            ->unique();

        return $this->tournament->players
            ->where('plays_doubles', true)
            ->reject(fn (Player $player) => $teamedPlayerIds->contains($player->id))
            ->values();
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
        return Tournament::withCount('players')
            ->with(['games' => fn ($q) => $q->select('id', 'tournament_id', 'completed', 'is_final', 'is_doubles')])
            ->orderByDesc('start_date')
            ->get();
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
