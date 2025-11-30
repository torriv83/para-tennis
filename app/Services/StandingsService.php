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
        $this->processGames($standings);
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

    protected function initializeStandings(): array
    {
        $standings = [];

        foreach ($this->tournament->players as $player) {
            $standings[$player->id] = [
                'player' => $player,
                'played' => 0,
                'wins' => 0,
                'losses' => 0,
                'sets_won' => 0,
                'sets_lost' => 0,
                'games_won' => 0,
                'games_lost' => 0,
            ];
        }

        return $standings;
    }

    protected function initializeStandingsWithIds(): array
    {
        $standings = [];

        foreach ($this->tournament->players as $player) {
            $standings[$player->id] = [
                'player_id' => $player->id,
                'player_name' => $player->name,
                'wins' => 0,
                'losses' => 0,
                'sets_won' => 0,
                'sets_lost' => 0,
                'games_won' => 0,
                'games_lost' => 0,
            ];
        }

        return $standings;
    }

    protected function processGames(array &$standings): void
    {
        $games = $this->tournament->games
            ->where('completed', true)
            ->where('is_final', false);

        foreach ($games as $game) {
            $p1Id = $game->player1_id;
            $p2Id = $game->player2_id;

            if ($game->is_walkover) {
                $standings[$game->walkover_winner_id]['wins']++;

                continue;
            }

            $standings[$p1Id]['played']++;
            $standings[$p2Id]['played']++;

            $standings[$p1Id]['sets_won'] += $game->player1_sets;
            $standings[$p1Id]['sets_lost'] += $game->player2_sets;
            $standings[$p2Id]['sets_won'] += $game->player2_sets;
            $standings[$p2Id]['sets_lost'] += $game->player1_sets;

            $standings[$p1Id]['games_won'] += $game->player1_games;
            $standings[$p1Id]['games_lost'] += $game->player2_games;
            $standings[$p2Id]['games_won'] += $game->player2_games;
            $standings[$p2Id]['games_lost'] += $game->player1_games;

            if ($game->player1_sets > $game->player2_sets) {
                $standings[$p1Id]['wins']++;
                $standings[$p2Id]['losses']++;
            } else {
                $standings[$p2Id]['wins']++;
                $standings[$p1Id]['losses']++;
            }
        }
    }

    protected function processGameData(array &$standings, array $game): void
    {
        $p1Id = $game['player1_id'];
        $p2Id = $game['player2_id'];

        if (! isset($standings[$p1Id]) || ! isset($standings[$p2Id])) {
            return;
        }

        if (! empty($game['is_walkover']) && ! empty($game['walkover_winner_id'])) {
            $standings[$game['walkover_winner_id']]['wins']++;

            return;
        }

        $standings[$p1Id]['sets_won'] += $game['player1_sets'];
        $standings[$p1Id]['sets_lost'] += $game['player2_sets'];
        $standings[$p2Id]['sets_won'] += $game['player2_sets'];
        $standings[$p2Id]['sets_lost'] += $game['player1_sets'];

        $standings[$p1Id]['games_won'] += $game['player1_games'];
        $standings[$p1Id]['games_lost'] += $game['player2_games'];
        $standings[$p2Id]['games_won'] += $game['player2_games'];
        $standings[$p2Id]['games_lost'] += $game['player1_games'];

        if ($game['player1_sets'] > $game['player2_sets']) {
            $standings[$p1Id]['wins']++;
            $standings[$p2Id]['losses']++;
        } else {
            $standings[$p2Id]['wins']++;
            $standings[$p1Id]['losses']++;
        }
    }

    protected function sortStandings(array &$standings): void
    {
        usort($standings, function ($a, $b) {
            if ($a['wins'] !== $b['wins']) {
                return $b['wins'] - $a['wins'];
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
