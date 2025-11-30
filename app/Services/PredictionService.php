<?php

namespace App\Services;

use App\Models\Tournament;
use Illuminate\Support\Collection;

class PredictionService
{
    protected StandingsService $standingsService;

    public function __construct(
        public Tournament $tournament
    ) {
        $this->standingsService = new StandingsService($tournament);
    }

    public function getPredictions(): array
    {
        $incompleteGames = $this->tournament->games
            ->where('completed', false)
            ->where('is_final', false);

        if ($incompleteGames->isEmpty()) {
            return $this->getFinalStandingsPredictions();
        }

        $scenarios = $this->generateAllScenarios($incompleteGames);
        $playerPredictions = [];

        foreach ($this->tournament->players as $player) {
            $playerPredictions[$player->id] = $this->analyzePlayerScenarios($player, $scenarios);
        }

        return $playerPredictions;
    }

    protected function getFinalStandingsPredictions(): array
    {
        $standings = $this->calculateStandings($this->tournament->games->toArray());
        $predictions = [];

        foreach ($standings as $index => $standing) {
            $position = $index + 1;
            $predictions[$standing['player_id']] = [
                'position' => $position,
                'scenarios' => [],
                'summary' => __('messages.position_place_final', ['position' => $this->getPositionLabel($position)]),
                'clinched' => true,
                'best_position' => $position,
                'worst_position' => $position,
            ];
        }

        return $predictions;
    }

    protected function generateAllScenarios(Collection $incompleteGames): array
    {
        $games = $incompleteGames->values()->all();
        $outcomes = [];

        // Each game can have 4 outcomes: 2-0, 2-1, 1-2, 0-2
        $possibleResults = [
            ['p1_sets' => 2, 'p2_sets' => 0, 'p1_games' => 12, 'p2_games' => 4],
            ['p1_sets' => 2, 'p2_sets' => 1, 'p1_games' => 14, 'p2_games' => 10],
            ['p1_sets' => 1, 'p2_sets' => 2, 'p1_games' => 10, 'p2_games' => 14],
            ['p1_sets' => 0, 'p2_sets' => 2, 'p1_games' => 4, 'p2_games' => 12],
        ];

        // Generate all combinations
        $totalCombinations = pow(count($possibleResults), count($games));

        // Limit to prevent memory issues (max 256 scenarios = 4^4 games)
        if ($totalCombinations > 256) {
            return $this->generateSampledScenarios($games, $possibleResults);
        }

        $scenarios = [];
        for ($i = 0; $i < $totalCombinations; $i++) {
            $scenario = [];
            $temp = $i;
            foreach ($games as $game) {
                $resultIndex = $temp % count($possibleResults);
                $temp = intdiv($temp, count($possibleResults));
                $result = $possibleResults[$resultIndex];
                $scenario[] = [
                    'game_id' => $game->id,
                    'player1_id' => $game->player1_id,
                    'player2_id' => $game->player2_id,
                    'player1_sets' => $result['p1_sets'],
                    'player2_sets' => $result['p2_sets'],
                    'player1_games' => $result['p1_games'],
                    'player2_games' => $result['p2_games'],
                ];
            }
            $scenarios[] = $scenario;
        }

        return $scenarios;
    }

    protected function generateSampledScenarios(array $games, array $possibleResults): array
    {
        // When there are too many combinations, sample key scenarios
        $scenarios = [];

        // Generate BEST case scenarios (player wins all their remaining games)
        foreach ($this->tournament->players as $player) {
            $scenario = [];
            foreach ($games as $game) {
                if ($game->player1_id === $player->id) {
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 2,
                        'player2_sets' => 0,
                        'player1_games' => 12,
                        'player2_games' => 4,
                    ];
                } elseif ($game->player2_id === $player->id) {
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 0,
                        'player2_sets' => 2,
                        'player1_games' => 4,
                        'player2_games' => 12,
                    ];
                } else {
                    // Other games: assume player1 wins
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 2,
                        'player2_sets' => 0,
                        'player1_games' => 12,
                        'player2_games' => 4,
                    ];
                }
            }
            $scenarios[] = $scenario;
        }

        // Generate WORST case scenarios (player loses all their remaining games)
        foreach ($this->tournament->players as $player) {
            $scenario = [];
            foreach ($games as $game) {
                if ($game->player1_id === $player->id) {
                    // This player loses as player1
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 0,
                        'player2_sets' => 2,
                        'player1_games' => 4,
                        'player2_games' => 12,
                    ];
                } elseif ($game->player2_id === $player->id) {
                    // This player loses as player2
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 2,
                        'player2_sets' => 0,
                        'player1_games' => 12,
                        'player2_games' => 4,
                    ];
                } else {
                    // Other games: assume player2 wins (different from best case)
                    $scenario[] = [
                        'game_id' => $game->id,
                        'player1_id' => $game->player1_id,
                        'player2_id' => $game->player2_id,
                        'player1_sets' => 0,
                        'player2_sets' => 2,
                        'player1_games' => 4,
                        'player2_games' => 12,
                    ];
                }
            }
            $scenarios[] = $scenario;
        }

        return $scenarios;
    }

    protected function analyzePlayerScenarios($player, array $scenarios): array
    {
        $positions = [];
        $scenarioDetails = [];

        foreach ($scenarios as $scenario) {
            $standings = $this->calculateStandingsWithScenario($scenario);
            $position = $this->getPlayerPosition($player->id, $standings);
            $positions[] = $position;

            // Track what this player did in this scenario and external results
            $playerResults = $this->getPlayerResultsInScenario($player->id, $scenario);
            $externalResults = $this->getExternalResultsInScenario($player->id, $scenario);
            $scenarioDetails[] = [
                'position' => $position,
                'results' => $playerResults,
                'external_results' => $externalResults,
                'standings' => $standings,
            ];
        }

        $bestPosition = min($positions);
        $worstPosition = max($positions);

        return [
            'best_position' => $bestPosition,
            'worst_position' => $worstPosition,
            'clinched' => $bestPosition === $worstPosition,
            'summary' => $this->generateSummary($player, $bestPosition, $worstPosition, $scenarioDetails),
            'scenarios' => $this->generateDetailedScenarios($player, $scenarioDetails, $bestPosition),
        ];
    }

    protected function calculateStandingsWithScenario(array $scenario): array
    {
        // Combine completed games with scenario
        $completedGames = $this->tournament->games
            ->where('completed', true)
            ->where('is_final', false)
            ->map(fn ($g) => [
                'player1_id' => $g->player1_id,
                'player2_id' => $g->player2_id,
                'player1_sets' => $g->player1_sets,
                'player2_sets' => $g->player2_sets,
                'player1_games' => $g->player1_games,
                'player2_games' => $g->player2_games,
                'is_walkover' => $g->is_walkover,
                'walkover_winner_id' => $g->walkover_winner_id,
            ])
            ->toArray();

        $allGames = array_merge($completedGames, $scenario);

        return $this->calculateStandings($allGames);
    }

    protected function calculateStandings(array $games): array
    {
        return $this->standingsService->calculateFromGameData($games);
    }

    protected function getPlayerPosition(int $playerId, array $standings): int
    {
        foreach ($standings as $index => $standing) {
            if ($standing['player_id'] === $playerId) {
                return $index + 1;
            }
        }

        return count($standings);
    }

    protected function getPlayerResultsInScenario(int $playerId, array $scenario): array
    {
        $results = [];
        foreach ($scenario as $game) {
            if ($game['player1_id'] === $playerId) {
                $results[] = [
                    'opponent_id' => $game['player2_id'],
                    'won' => $game['player1_sets'] > $game['player2_sets'],
                    'sets' => $game['player1_sets'].'-'.$game['player2_sets'],
                ];
            } elseif ($game['player2_id'] === $playerId) {
                $results[] = [
                    'opponent_id' => $game['player1_id'],
                    'won' => $game['player2_sets'] > $game['player1_sets'],
                    'sets' => $game['player2_sets'].'-'.$game['player1_sets'],
                ];
            }
        }

        return $results;
    }

    protected function getExternalResultsInScenario(int $playerId, array $scenario): array
    {
        $results = [];
        foreach ($scenario as $game) {
            if ($game['player1_id'] !== $playerId && $game['player2_id'] !== $playerId) {
                $player1 = $this->tournament->players->find($game['player1_id']);
                $player2 = $this->tournament->players->find($game['player2_id']);
                $p1Wins = $game['player1_sets'] > $game['player2_sets'];
                $results[] = [
                    'player1_id' => $game['player1_id'],
                    'player2_id' => $game['player2_id'],
                    'player1_name' => $player1->name,
                    'player2_name' => $player2->name,
                    'winner_id' => $p1Wins ? $game['player1_id'] : $game['player2_id'],
                    'sets' => $game['player1_sets'].'-'.$game['player2_sets'],
                ];
            }
        }

        return $results;
    }

    protected function generateSummary(mixed $player, int $bestPosition, int $worstPosition, array $scenarioDetails): string
    {
        if ($bestPosition === $worstPosition) {
            return __('messages.clinched_position', ['position' => $this->getPositionLabel($bestPosition)]);
        }

        $incompleteGames = $this->tournament->games
            ->where('completed', false)
            ->where('is_final', false);

        $playerGames = $incompleteGames->filter(
            fn ($g) => $g->player1_id === $player->id || $g->player2_id === $player->id
        );

        if ($playerGames->isEmpty()) {
            return __('messages.waiting_on_results', [
                'best' => $this->getPositionLabel($bestPosition),
                'worst' => $this->getPositionLabel($worstPosition),
            ]);
        }

        return __('messages.can_finish_range', [
            'best' => $this->getPositionLabel($bestPosition),
            'worst' => $this->getPositionLabel($worstPosition),
        ]);
    }

    protected function generateDetailedScenarios(mixed $player, array $scenarioDetails, int $bestPosition): array
    {
        $detailed = [];

        // Group scenarios by position first
        $byPosition = [];
        foreach ($scenarioDetails as $scenario) {
            $pos = $scenario['position'];
            if (! isset($byPosition[$pos])) {
                $byPosition[$pos] = [];
            }
            $byPosition[$pos][] = $scenario;
        }

        ksort($byPosition);

        foreach ($byPosition as $position => $positionScenarios) {
            // Find minimal conditions for this position
            $minimalConditions = $this->findMinimalConditionsForPosition($positionScenarios, $scenarioDetails);

            foreach ($minimalConditions as $condition) {
                $detailed[] = [
                    'position' => $position,
                    'position_label' => $this->getPositionLabel($position),
                    'conditions' => $condition['conditions'],
                    'external_conditions' => $condition['external_conditions'],
                    'count' => $condition['count'],
                ];
            }
        }

        return $detailed;
    }

    protected function findMinimalConditionsForPosition(array $positionScenarios, array $allScenarios): array
    {
        // Get all opponents the player faces
        $opponents = [];
        foreach ($positionScenarios[0]['results'] as $result) {
            $opponents[$result['opponent_id']] = true;
        }

        // For each opponent, check if their result matters for this position
        $requiredOpponents = [];
        foreach (array_keys($opponents) as $opponentId) {
            $opponentMatters = false;

            // Check if changing ONLY this opponent's result (keeping others same) would change position
            foreach ($positionScenarios as $scenario) {
                // Get the results against other opponents
                $otherResults = [];
                $thisOpponentWon = null;
                foreach ($scenario['results'] as $result) {
                    if ($result['opponent_id'] === $opponentId) {
                        $thisOpponentWon = $result['won'];
                    } else {
                        $otherResults[$result['opponent_id']] = $result['won'];
                    }
                }

                // Find scenarios with same other-opponent results but different result against this opponent
                foreach ($allScenarios as $otherScenario) {
                    $otherOpponentWon = null;
                    $sameOtherResults = true;

                    foreach ($otherScenario['results'] as $otherResult) {
                        if ($otherResult['opponent_id'] === $opponentId) {
                            $otherOpponentWon = $otherResult['won'];
                        } elseif (! isset($otherResults[$otherResult['opponent_id']]) ||
                                  $otherResults[$otherResult['opponent_id']] !== $otherResult['won']) {
                            $sameOtherResults = false;
                            break;
                        }
                    }

                    // Same other results, different result against this opponent, different position?
                    if ($sameOtherResults && $otherOpponentWon !== $thisOpponentWon &&
                        $otherScenario['position'] !== $scenario['position']) {
                        $opponentMatters = true;
                        break 2;
                    }
                }
            }

            if ($opponentMatters) {
                $requiredOpponents[$opponentId] = true;
            }
        }

        // If no opponents are required, all opponents matter for distinguishing scenarios
        if (empty($requiredOpponents)) {
            $requiredOpponents = $opponents;
        }

        // Now group by the required opponents' results (win/loss only, not score)
        $groupedByWinLoss = [];
        foreach ($positionScenarios as $scenario) {
            $key = $this->createWinLossKey($scenario['results'], $requiredOpponents);
            if (! isset($groupedByWinLoss[$key])) {
                $groupedByWinLoss[$key] = [];
            }
            $groupedByWinLoss[$key][] = $scenario;
        }

        $conditions = [];
        foreach ($groupedByWinLoss as $winLossKey => $scenarios) {
            // Check if score matters within this win/loss group
            $conditionResults = $this->determineRequiredScores($scenarios, $allScenarios, $requiredOpponents);

            // Check for external conditions
            $externalConditions = $this->findDifferentiatingExternalConditions($scenarios, $allScenarios);

            $conditions[] = [
                'conditions' => $this->formatConditionsWithCollapsing($conditionResults),
                'external_conditions' => $this->formatExternalConditions($externalConditions),
                'count' => count($scenarios),
            ];
        }

        return $conditions;
    }

    protected function createWinLossKey(array $results, array $requiredOpponents): string
    {
        $parts = [];
        foreach ($results as $result) {
            if (isset($requiredOpponents[$result['opponent_id']])) {
                $parts[] = $result['opponent_id'].':'.($result['won'] ? 'W' : 'L');
            }
        }
        sort($parts);

        return implode('|', $parts);
    }

    protected function determineRequiredScores(array $scenarios, array $allScenarios, array $requiredOpponents): array
    {
        if (empty($scenarios)) {
            return [];
        }

        $firstResults = $scenarios[0]['results'];
        $targetPosition = $scenarios[0]['position'];
        $conditionResults = [];

        foreach ($firstResults as $result) {
            if (! isset($requiredOpponents[$result['opponent_id']])) {
                continue; // Skip opponents that don't matter
            }

            // Check if score matters: do all score variations within same win/loss lead to same position?
            $scoreMatters = false;

            // Find all scenarios with same win/loss for this opponent but different scores
            foreach ($allScenarios as $otherScenario) {
                foreach ($otherScenario['results'] as $otherResult) {
                    if ($otherResult['opponent_id'] === $result['opponent_id'] &&
                        $otherResult['won'] === $result['won'] &&
                        $otherResult['sets'] !== $result['sets']) {
                        // Same win/loss, different score - check if position differs
                        if ($otherScenario['position'] !== $targetPosition) {
                            $scoreMatters = true;
                            break 2;
                        }
                    }
                }
            }

            $conditionResults[] = [
                'opponent_id' => $result['opponent_id'],
                'won' => $result['won'],
                'sets' => $result['sets'],
                'score_matters' => $scoreMatters,
            ];
        }

        return $conditionResults;
    }

    protected function formatConditionsWithCollapsing(array $results): string
    {
        $conditions = [];
        foreach ($results as $result) {
            $opponent = $this->tournament->players->find($result['opponent_id']);
            $verb = $result['won'] ? __('messages.beat') : __('messages.lose_to');

            if ($result['score_matters']) {
                $conditions[] = $verb.' '.$opponent->name.' '.$result['sets'];
            } else {
                $conditions[] = $verb.' '.$opponent->name;
            }
        }

        return implode(', ', $conditions);
    }

    protected function findDifferentiatingExternalConditions(array $targetScenarios, array $allScenarios): array
    {
        if (empty($targetScenarios[0]['external_results'])) {
            return [];
        }

        // Find external conditions that are consistent within targetScenarios
        // but different from other scenarios
        $targetExternal = $targetScenarios[0]['external_results'];
        $conditions = [];

        foreach ($targetExternal as $index => $extResult) {
            // Check if this external result is the same across all target scenarios
            $consistentInTarget = true;
            foreach ($targetScenarios as $scenario) {
                if (! isset($scenario['external_results'][$index])) {
                    $consistentInTarget = false;
                    break;
                }
                if ($scenario['external_results'][$index]['winner_id'] !== $extResult['winner_id']) {
                    $consistentInTarget = false;
                    break;
                }
            }

            if ($consistentInTarget) {
                // Check if it differs from scenarios leading to other positions
                $differsFromOthers = false;
                foreach ($allScenarios as $scenario) {
                    if (in_array($scenario, $targetScenarios, true)) {
                        continue;
                    }
                    if (! isset($scenario['external_results'][$index])) {
                        continue;
                    }
                    if ($scenario['external_results'][$index]['winner_id'] !== $extResult['winner_id']) {
                        $differsFromOthers = true;
                        break;
                    }
                }

                if ($differsFromOthers) {
                    $conditions[] = $extResult;
                }
            }
        }

        return $conditions;
    }

    protected function formatConditions(array $results): string
    {
        $conditions = [];
        foreach ($results as $result) {
            $opponent = $this->tournament->players->find($result['opponent_id']);
            $verb = $result['won'] ? __('messages.beat') : __('messages.lose_to');
            $conditions[] = $verb.' '.$opponent->name.' '.$result['sets'];
        }

        return implode(', ', $conditions);
    }

    protected function formatExternalConditions(array $externalResults): string
    {
        if (empty($externalResults)) {
            return '';
        }

        $conditions = [];
        foreach ($externalResults as $result) {
            $winnerName = $result['winner_id'] === $result['player1_id']
                ? $result['player1_name']
                : $result['player2_name'];
            $loserName = $result['winner_id'] === $result['player1_id']
                ? $result['player2_name']
                : $result['player1_name'];
            $conditions[] = __('messages.player_beats_player', [
                'winner' => $winnerName,
                'loser' => $loserName,
            ]);
        }

        return implode(', ', $conditions);
    }

    protected function getPositionLabel(int $position): string
    {
        return match ($position) {
            1 => __('messages.position_1st'),
            2 => __('messages.position_2nd'),
            3 => __('messages.position_3rd'),
            default => __('messages.position_nth', ['n' => $position]),
        };
    }
}
