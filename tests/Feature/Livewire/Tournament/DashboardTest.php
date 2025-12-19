<?php

use App\Livewire\Tournament\CreateTournament;
use App\Livewire\Tournament\Dashboard;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use Livewire\Livewire;

beforeEach(function () {
    $this->tournament = Tournament::factory()->create();
});

it('can add a player to a tournament', function () {
    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newPlayerName', 'Alice')
        ->call('addPlayer');

    expect($this->tournament->fresh()->players)->toHaveCount(1)
        ->and($this->tournament->fresh()->players->first()->name)->toBe('Alice');
});

it('prevents adding duplicate players', function () {
    $this->tournament->players()->create(['name' => 'Alice']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newPlayerName', 'Alice')
        ->call('addPlayer');

    expect($this->tournament->fresh()->players)->toHaveCount(1);
});

it('can import players from history', function () {
    $otherTournament = Tournament::factory()->create();
    $otherTournament->players()->createMany([
        ['name' => 'Bob'],
        ['name' => 'Carol'],
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('selectedPlayers', ['Bob', 'Carol'])
        ->call('importPlayers');

    $playerNames = $this->tournament->fresh()->players->pluck('name')->toArray();

    expect($this->tournament->fresh()->players)->toHaveCount(2)
        ->and($playerNames)->toContain('Bob')
        ->and($playerNames)->toContain('Carol');
});

it('skips importing players that already exist', function () {
    $this->tournament->players()->create(['name' => 'Alice']);

    $otherTournament = Tournament::factory()->create();
    $otherTournament->players()->create(['name' => 'Alice']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('selectedPlayers', ['Alice'])
        ->call('importPlayers');

    expect($this->tournament->fresh()->players)->toHaveCount(1);
});

it('can record a walkover', function () {
    $player1 = $this->tournament->players()->create(['name' => 'Alice']);
    $player2 = $this->tournament->players()->create(['name' => 'Bob']);

    $game = $this->tournament->games()->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('recordWalkover', $game->id, $player1->id);

    $game->refresh();

    expect($game->is_walkover)->toBeTrue()
        ->and($game->walkover_winner_id)->toBe($player1->id)
        ->and($game->completed)->toBeTrue()
        ->and($game->player1_sets)->toBe(0)
        ->and($game->player2_sets)->toBe(0);
});

it('can clear a walkover', function () {
    $player1 = $this->tournament->players()->create(['name' => 'Alice']);
    $player2 = $this->tournament->players()->create(['name' => 'Bob']);

    $game = $this->tournament->games()->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
        'is_walkover' => true,
        'walkover_winner_id' => $player1->id,
        'completed' => true,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('clearWalkover', $game->id);

    $game->refresh();

    expect($game->is_walkover)->toBeFalse()
        ->and($game->walkover_winner_id)->toBeNull()
        ->and($game->completed)->toBeFalse();
});

it('calculates standings correctly with walkover', function () {
    $player1 = $this->tournament->players()->create(['name' => 'Alice']);
    $player2 = $this->tournament->players()->create(['name' => 'Bob']);

    $this->tournament->games()->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
        'is_walkover' => true,
        'walkover_winner_id' => $player1->id,
        'completed' => true,
        'player1_sets' => 0,
        'player2_sets' => 0,
        'player1_games' => 0,
        'player2_games' => 0,
    ]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament);

    $standings = $component->instance()->standings;
    $aliceStanding = collect($standings)->firstWhere('player.id', $player1->id);
    $bobStanding = collect($standings)->firstWhere('player.id', $player2->id);

    expect($aliceStanding['wins'])->toBe(1)
        ->and($aliceStanding['played'])->toBe(0)
        ->and($aliceStanding['losses'])->toBe(0)
        ->and($bobStanding['wins'])->toBe(0)
        ->and($bobStanding['played'])->toBe(0)
        ->and($bobStanding['losses'])->toBe(0); // Walkover: no match played, no loss recorded
});

it('can create a tournament', function () {
    Livewire::test(CreateTournament::class)
        ->set('tournamentName', 'Spring Championship')
        ->set('startDate', '2025-03-01')
        ->set('endDate', '2025-03-03')
        ->set('tournamentFormat', 'round_robin')
        ->call('createTournament');

    $this->assertDatabaseHas('tournaments', [
        'name' => 'Spring Championship',
        'format' => 'round_robin',
    ]);
});

it('requires tournament name for creation', function () {
    Livewire::test(CreateTournament::class)
        ->set('tournamentName', '')
        ->set('startDate', '2025-03-01')
        ->set('endDate', '2025-03-03')
        ->set('tournamentFormat', 'round_robin')
        ->call('createTournament')
        ->assertHasErrors(['tournamentName' => 'required']);
});

it('requires end date to be after start date', function () {
    Livewire::test(CreateTournament::class)
        ->set('tournamentName', 'Test Tournament')
        ->set('startDate', '2025-03-05')
        ->set('endDate', '2025-03-01')
        ->set('tournamentFormat', 'round_robin')
        ->call('createTournament')
        ->assertHasErrors(['endDate' => 'after_or_equal']);
});

it('can update tournament', function () {
    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('startEditingTournament')
        ->set('editName', 'Updated Name')
        ->set('editStartDate', '2025-04-01')
        ->set('editEndDate', '2025-04-03')
        ->set('editFormat', 'round_robin_finals')
        ->call('updateTournament');

    $this->tournament->refresh();

    expect($this->tournament->name)->toBe('Updated Name')
        ->and($this->tournament->format->value)->toBe('round_robin_finals');
});

it('can delete tournament', function () {
    $tournamentId = $this->tournament->id;

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('deleteTournament');

    $this->assertDatabaseMissing('tournaments', ['id' => $tournamentId]);
});

it('can generate schedule', function () {
    Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    Player::factory()->for($this->tournament)->create(['name' => 'Bob']);
    Player::factory()->for($this->tournament)->create(['name' => 'Carol']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('generateSchedule');

    // 3 players = 3 games (round robin)
    expect($this->tournament->fresh()->games)->toHaveCount(3);
});

it('can update game result', function () {
    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);

    $game = Game::factory()->for($this->tournament)->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('updateGameResult', $game->id, 2, 1, 14, 10);

    $game->refresh();

    expect($game->completed)->toBeTrue()
        ->and($game->player1_sets)->toBe(2)
        ->and($game->player2_sets)->toBe(1)
        ->and($game->player1_games)->toBe(14)
        ->and($game->player2_games)->toBe(10);
});

it('does not save game result when sets are tied 1-1 without winner', function () {
    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);

    $game = Game::factory()->for($this->tournament)->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
        'completed' => false,
    ]);

    // Attempting to save with 1-1 sets (no winner determined)
    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('updateGameResult', $game->id, 1, 1, 10, 10);

    $game->refresh();

    // Game should NOT be saved as there's no winner
    expect($game->completed)->toBeFalse()
        ->and($game->player1_sets)->toBe(0)
        ->and($game->player2_sets)->toBe(0);
});

it('can remove player', function () {
    $player = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('removePlayer', $player->id);

    $this->assertDatabaseMissing('players', ['id' => $player->id]);
});

// Doubles Feature Tests

it('can create a tournament with doubles enabled', function () {
    Livewire::test(CreateTournament::class)
        ->set('tournamentName', 'Doubles Championship')
        ->set('startDate', '2025-03-01')
        ->set('endDate', '2025-03-03')
        ->set('tournamentFormat', 'round_robin')
        ->set('hasDoubles', true)
        ->call('createTournament');

    $this->assertDatabaseHas('tournaments', [
        'name' => 'Doubles Championship',
        'has_doubles' => true,
    ]);
});

it('can create a doubles match', function () {
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);
    $player3 = Player::factory()->for($this->tournament)->create(['name' => 'Carol']);
    $player4 = Player::factory()->for($this->tournament)->create(['name' => 'Dave']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('showDoublesForm', true)
        ->set('doublesTeam1Player1', $player1->id)
        ->set('doublesTeam1Player2', $player2->id)
        ->set('doublesTeam2Player1', $player3->id)
        ->set('doublesTeam2Player2', $player4->id)
        ->call('createDoublesMatch');

    $doublesGame = $this->tournament->fresh()->games->first();

    expect($doublesGame)->not->toBeNull()
        ->and($doublesGame->is_doubles)->toBeTrue()
        ->and($doublesGame->player1_id)->toBe($player1->id)
        ->and($doublesGame->player1_partner_id)->toBe($player2->id)
        ->and($doublesGame->player2_id)->toBe($player3->id)
        ->and($doublesGame->player2_partner_id)->toBe($player4->id);
});

it('prevents creating doubles match when tournament does not have doubles enabled', function () {
    $this->tournament->update(['has_doubles' => false]);

    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);
    $player3 = Player::factory()->for($this->tournament)->create(['name' => 'Carol']);
    $player4 = Player::factory()->for($this->tournament)->create(['name' => 'Dave']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('doublesTeam1Player1', $player1->id)
        ->set('doublesTeam1Player2', $player2->id)
        ->set('doublesTeam2Player1', $player3->id)
        ->set('doublesTeam2Player2', $player4->id)
        ->call('createDoublesMatch');

    expect($this->tournament->fresh()->games)->toHaveCount(0);
});

it('prevents creating doubles match with duplicate players', function () {
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);
    $player3 = Player::factory()->for($this->tournament)->create(['name' => 'Carol']);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('doublesTeam1Player1', $player1->id)
        ->set('doublesTeam1Player2', $player2->id)
        ->set('doublesTeam2Player1', $player1->id) // Duplicate
        ->set('doublesTeam2Player2', $player3->id)
        ->call('createDoublesMatch');

    expect($this->tournament->fresh()->games)->toHaveCount(0);
});

it('returns doubles match via computed property', function () {
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create();
    $player2 = Player::factory()->for($this->tournament)->create();
    $player3 = Player::factory()->for($this->tournament)->create();
    $player4 = Player::factory()->for($this->tournament)->create();

    $doublesGame = Game::factory()->for($this->tournament)->create([
        'player1_id' => $player1->id,
        'player1_partner_id' => $player2->id,
        'player2_id' => $player3->id,
        'player2_partner_id' => $player4->id,
        'is_doubles' => true,
    ]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament);

    $match = $component->instance()->doublesMatch;

    expect($match)->not->toBeNull()
        ->and($match->id)->toBe($doublesGame->id)
        ->and($match->is_doubles)->toBeTrue();
});

it('can swap players in a doubles match including partners', function () {
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice']);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob']);
    $player3 = Player::factory()->for($this->tournament)->create(['name' => 'Carol']);
    $player4 = Player::factory()->for($this->tournament)->create(['name' => 'Dave']);

    $game = Game::factory()->for($this->tournament)->create([
        'player1_id' => $player1->id,
        'player1_partner_id' => $player2->id,
        'player2_id' => $player3->id,
        'player2_partner_id' => $player4->id,
        'is_doubles' => true,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'completed' => true,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('swapPlayers', $game->id);

    $game->refresh();

    expect($game->player1_id)->toBe($player3->id)
        ->and($game->player1_partner_id)->toBe($player4->id)
        ->and($game->player2_id)->toBe($player1->id)
        ->and($game->player2_partner_id)->toBe($player2->id)
        ->and($game->player1_sets)->toBe(1)
        ->and($game->player2_sets)->toBe(2);
});

it('walkover does not count as loss for withdrawing player', function () {
    $player1 = $this->tournament->players()->create(['name' => 'Alice']);
    $player2 = $this->tournament->players()->create(['name' => 'Bob']);

    $this->tournament->games()->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
        'is_walkover' => true,
        'walkover_winner_id' => $player1->id,
        'completed' => true,
        'player1_sets' => 0,
        'player2_sets' => 0,
        'player1_games' => 0,
        'player2_games' => 0,
    ]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament);

    $standings = $component->instance()->standings;
    $aliceStanding = collect($standings)->firstWhere('player.id', $player1->id);
    $bobStanding = collect($standings)->firstWhere('player.id', $player2->id);

    // Walkover: winner advances (gets a win), but no match played so no loss recorded
    expect($aliceStanding['wins'])->toBe(1)
        ->and($aliceStanding['losses'])->toBe(0)
        ->and($bobStanding['wins'])->toBe(0)
        ->and($bobStanding['losses'])->toBe(0);
});
