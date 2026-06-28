<?php

namespace App\Services;

use App\Models\Tournament;

class StandingsService
{
    public function __construct(
        public Tournament $tournament
    ) {}

    public function calculate(): array
    {
        $standings = $this->initializeStandings();
        $this->processGamesFor($standings, false);
        $this->sortStandings($standings);

        return $standings;
    }

    public function calculateDoubles(): array
    {
        $standings = $this->initializeDoublesStandings();
        $this->processGamesFor($standings, true);
        $this->sortStandings($standings);

        return $standings;
    }

    public function calculateFromGameData(array $games): array
    {
        $standings = $this->initializeStandingsWithIds();

        foreach ($games as $game) {
            $this->processGameData($standings, $game);
        }

        $this->sortStandings($standings);

        return array_values($standings);
    }

    /**
     * @param  array<string, mixed>  $identity
     * @return array<string, mixed>
     */
    protected function emptyStatsRow(array $identity): array
    {
        return array_merge($identity, [
            'played' => 0,
            'wins' => 0,
            'losses' => 0,
            'points' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
            'games_won' => 0,
            'games_lost' => 0,
        ]);
    }

    protected function initializeStandings(): array
    {
        $standings = [];

        foreach ($this->tournament->players->where('plays_singles', true) as $player) {
            $standings[$player->id] = $this->emptyStatsRow(['player' => $player]);
        }

        return $standings;
    }

    protected function initializeDoublesStandings(): array
    {
        $standings = [];

        foreach ($this->tournament->teams as $team) {
            $standings[$team->id] = $this->emptyStatsRow(['team' => $team]);
        }

        return $standings;
    }

    protected function initializeStandingsWithIds(): array
    {
        $standings = [];

        foreach ($this->tournament->players->where('plays_singles', true) as $player) {
            $standings[$player->id] = $this->emptyStatsRow([
                'player_id' => $player->id,
                'player_name' => $player->name,
            ]);
        }

        return $standings;
    }

    protected function processGamesFor(array &$standings, bool $isDoubles): void
    {
        $games = $this->tournament->games
            ->where('completed', true)
            ->where('is_final', false)
            ->where('is_doubles', $isDoubles);

        foreach ($games as $game) {
            $walkoverWinnerId = $isDoubles ? $game->walkover_winner_team_id : $game->walkover_winner_id;

            $this->accumulate(
                $standings,
                $isDoubles ? $game->team1_id : $game->player1_id,
                $isDoubles ? $game->team2_id : $game->player2_id,
                $game->player1_sets,
                $game->player2_sets,
                $game->player1_games,
                $game->player2_games,
                $game->is_walkover ? $walkoverWinnerId : null,
            );
        }
    }

    protected function accumulate(
        array &$standings,
        ?int $c1Id,
        ?int $c2Id,
        int $c1Sets,
        int $c2Sets,
        int $c1Games,
        int $c2Games,
        ?int $walkoverWinnerId,
    ): void {
        if (! isset($standings[$c1Id]) || ! isset($standings[$c2Id])) {
            return;
        }

        if ($walkoverWinnerId !== null) {
            if (isset($standings[$walkoverWinnerId])) {
                $standings[$walkoverWinnerId]['wins']++;
                $standings[$walkoverWinnerId]['points'] += 2;
            }

            return;
        }

        $standings[$c1Id]['played']++;
        $standings[$c2Id]['played']++;

        $standings[$c1Id]['sets_won'] += $c1Sets;
        $standings[$c1Id]['sets_lost'] += $c2Sets;
        $standings[$c2Id]['sets_won'] += $c2Sets;
        $standings[$c2Id]['sets_lost'] += $c1Sets;

        $standings[$c1Id]['games_won'] += $c1Games;
        $standings[$c1Id]['games_lost'] += $c2Games;
        $standings[$c2Id]['games_won'] += $c2Games;
        $standings[$c2Id]['games_lost'] += $c1Games;

        if ($c1Sets > $c2Sets) {
            $standings[$c1Id]['wins']++;
            $standings[$c1Id]['points'] += 2;
            $standings[$c2Id]['losses']++;
        } else {
            $standings[$c2Id]['wins']++;
            $standings[$c2Id]['points'] += 2;
            $standings[$c1Id]['losses']++;
        }
    }

    protected function processGameData(array &$standings, array $game): void
    {
        $this->accumulate(
            $standings,
            $game['player1_id'],
            $game['player2_id'],
            $game['player1_sets'] ?? 0,
            $game['player2_sets'] ?? 0,
            $game['player1_games'] ?? 0,
            $game['player2_games'] ?? 0,
            (! empty($game['is_walkover']) && ! empty($game['walkover_winner_id'])) ? $game['walkover_winner_id'] : null,
        );
    }

    protected function sortStandings(array &$standings): void
    {
        usort($standings, function ($a, $b) {
            // Sort by points first
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }

            $aSetDiff = $a['sets_won'] - $a['sets_lost'];
            $bSetDiff = $b['sets_won'] - $b['sets_lost'];

            if ($aSetDiff !== $bSetDiff) {
                return $bSetDiff - $aSetDiff;
            }

            $aGameDiff = $a['games_won'] - $a['games_lost'];
            $bGameDiff = $b['games_won'] - $b['games_lost'];

            return $bGameDiff - $aGameDiff;
        });
    }
}
