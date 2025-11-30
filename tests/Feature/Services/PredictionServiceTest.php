<?php

use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\PredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates standings by wins', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);
    $carol = Player::factory()->for($tournament)->create(['name' => 'Carol']);

    // Alice beats Bob 2-0
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    // Alice beats Carol 2-1
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $carol->id,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'player1_games' => 14,
        'player2_games' => 10,
        'completed' => true,
    ]);

    // Bob beats Carol 2-0
    Game::factory()->for($tournament)->create([
        'player1_id' => $bob->id,
        'player2_id' => $carol->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    // Alice: 2 wins, Bob: 1 win, Carol: 0 wins
    expect($predictions[$alice->id]['position'])->toBe(1)
        ->and($predictions[$bob->id]['position'])->toBe(2)
        ->and($predictions[$carol->id]['position'])->toBe(3);
});

it('uses set difference as first tiebreaker', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);
    $carol = Player::factory()->for($tournament)->create(['name' => 'Carol']);

    // Alice beats Bob 2-0 (set diff +2)
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    // Carol beats Alice 2-1 (Alice set diff now +1, Carol +1)
    Game::factory()->for($tournament)->create([
        'player1_id' => $carol->id,
        'player2_id' => $alice->id,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'player1_games' => 14,
        'player2_games' => 10,
        'completed' => true,
    ]);

    // Bob beats Carol 2-0 (Bob set diff now 0, Carol now -1)
    Game::factory()->for($tournament)->create([
        'player1_id' => $bob->id,
        'player2_id' => $carol->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    // All have 1 win. Set diffs: Alice +1, Bob 0, Carol -1
    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    expect($predictions[$alice->id]['position'])->toBe(1)
        ->and($predictions[$bob->id]['position'])->toBe(2)
        ->and($predictions[$carol->id]['position'])->toBe(3);
});

it('uses game difference as second tiebreaker', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);
    $carol = Player::factory()->for($tournament)->create(['name' => 'Carol']);

    // Alice beats Bob 2-1 with close games (14-12)
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'player1_games' => 14,
        'player2_games' => 12,
        'completed' => true,
    ]);

    // Carol beats Alice 2-1 with close games (14-12)
    Game::factory()->for($tournament)->create([
        'player1_id' => $carol->id,
        'player2_id' => $alice->id,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'player1_games' => 14,
        'player2_games' => 12,
        'completed' => true,
    ]);

    // Bob beats Carol 2-1 with dominant games (14-8)
    Game::factory()->for($tournament)->create([
        'player1_id' => $bob->id,
        'player2_id' => $carol->id,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'player1_games' => 14,
        'player2_games' => 8,
        'completed' => true,
    ]);

    // All have 1 win, all have set diff 0
    // Game diffs: Alice +2-2=0, Bob +6-2=+4, Carol -6+2=-4
    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    expect($predictions[$bob->id]['position'])->toBe(1)
        ->and($predictions[$alice->id]['position'])->toBe(2)
        ->and($predictions[$carol->id]['position'])->toBe(3);
});

it('handles walkover wins correctly in standings', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);

    // Alice gets walkover win against Bob
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 0,
        'player2_sets' => 0,
        'player1_games' => 0,
        'player2_games' => 0,
        'is_walkover' => true,
        'walkover_winner_id' => $alice->id,
        'completed' => true,
    ]);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    // Alice should be 1st with walkover win
    expect($predictions[$alice->id]['position'])->toBe(1)
        ->and($predictions[$bob->id]['position'])->toBe(2);
});

it('shows clinched position when all games complete', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);

    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    expect($predictions[$alice->id]['clinched'])->toBeTrue()
        ->and($predictions[$bob->id]['clinched'])->toBeTrue();
});

it('calculates best and worst positions with incomplete games', function () {
    $tournament = Tournament::factory()->create();
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);

    // Incomplete game
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'completed' => false,
    ]);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    // Both can finish 1st or 2nd
    expect($predictions[$alice->id]['best_position'])->toBe(1)
        ->and($predictions[$alice->id]['worst_position'])->toBe(2)
        ->and($predictions[$alice->id]['clinched'])->toBeFalse()
        ->and($predictions[$bob->id]['best_position'])->toBe(1)
        ->and($predictions[$bob->id]['worst_position'])->toBe(2);
});

it('returns empty predictions for tournament with no games', function () {
    $tournament = Tournament::factory()->create();
    Player::factory()->for($tournament)->create(['name' => 'Alice']);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    expect($predictions)->toBeArray();
});

it('excludes final matches from round robin standings', function () {
    $tournament = Tournament::factory()->create(['format' => 'round_robin_finals']);
    $alice = Player::factory()->for($tournament)->create(['name' => 'Alice']);
    $bob = Player::factory()->for($tournament)->create(['name' => 'Bob']);

    // Round robin game - Alice wins
    Game::factory()->for($tournament)->create([
        'player1_id' => $alice->id,
        'player2_id' => $bob->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
        'is_final' => false,
    ]);

    // Final game - Bob wins (should not affect round robin standings)
    Game::factory()->for($tournament)->create([
        'player1_id' => $bob->id,
        'player2_id' => $alice->id,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
        'is_final' => true,
    ]);

    $tournament->load(['players', 'games']);
    $service = new PredictionService($tournament);
    $predictions = $service->getPredictions();

    // Alice should still be 1st in round robin standings
    expect($predictions[$alice->id]['position'])->toBe(1)
        ->and($predictions[$bob->id]['position'])->toBe(2);
});
