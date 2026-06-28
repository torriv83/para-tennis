<?php

use App\Livewire\Tournament\CreateTournament;
use App\Livewire\Tournament\Dashboard;
use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
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

it('pairs players imported in the same batch against each other', function () {
    $x = $this->tournament->players()->create(['name' => 'Xavier']);
    $y = $this->tournament->players()->create(['name' => 'Yara']);
    $this->tournament->games()->create(['player1_id' => $x->id, 'player2_id' => $y->id]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('selectedPlayers', ['Anna', 'Bjorn'])
        ->call('importPlayers');

    $tournament = $this->tournament->fresh()->load('games');
    $anna = $tournament->players->firstWhere('name', 'Anna');
    $bjorn = $tournament->players->firstWhere('name', 'Bjorn');

    $annaVsBjorn = $tournament->games->first(fn ($g) => in_array($g->player1_id, [$anna->id, $bjorn->id], true)
        && in_array($g->player2_id, [$anna->id, $bjorn->id], true));

    // 1 existing (X-Y) + X/Y vs Anna + X/Y vs Bjorn + Anna-Bjorn = 6 games.
    expect($tournament->games)->toHaveCount(6)
        ->and($annaVsBjorn)->not->toBeNull();
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

it('renders the doubles tab with schedule, standings and final for admins without errors', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true, 'doubles_format' => 'round_robin_finals']);

    $players = Player::factory()->count(6)->for($this->tournament)->create(['plays_doubles' => true]);
    Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);
    Team::factory()->for($this->tournament)->create(['player1_id' => $players[4]->id, 'player2_id' => $players[5]->id]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('activeTab', 'doubles')
        ->call('generateDoublesSchedule')
        ->assertOk()
        ->assertSee(__('messages.doubles_teams'))
        ->assertSee(__('messages.doubles_standings'))
        ->assertSee(__('messages.doubles_matches'))
        ->assertSee(__('messages.doubles_final'));
});

it('hides the teams management box from non-admin visitors', function () {
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(4)->for($this->tournament)->create(['plays_doubles' => true]);
    Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('activeTab', 'doubles')
        ->assertOk()
        ->assertDontSee(__('messages.doubles_teams'));
});

it('lets admins edit teams even after the schedule is generated', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(4)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('generateDoublesSchedule');

    // Schedule exists, yet removing a team is still allowed.
    $component->call('removeTeam', $teamA->id);

    expect($this->tournament->fresh()->teams)->toHaveCount(1)
        ->and($this->tournament->fresh()->teams->first()->id)->toBe($teamB->id);
});

it('prevents non-admin visitors from creating teams', function () {
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);
    $player2 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);

    // No actingAs() -> not an admin.
    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newTeamPlayer1', $player1->id)
        ->set('newTeamPlayer2', $player2->id)
        ->call('createTeam');

    expect($this->tournament->fresh()->teams)->toHaveCount(0);
});

it('disbands teams and deletes their doubles games when a player is removed', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(4)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);

    $this->tournament->games()->create([
        'team1_id' => $teamA->id,
        'team2_id' => $teamB->id,
        'is_doubles' => true,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('removePlayer', $players[0]->id);

    // Team A is disbanded and its doubles game removed -> no orphan game with a null team.
    expect($this->tournament->fresh()->teams)->toHaveCount(1)
        ->and($this->tournament->fresh()->teams->first()->id)->toBe($teamB->id)
        ->and($this->tournament->fresh()->games->where('is_doubles', true))->toHaveCount(0);
});

it('can create a doubles team from two doubles players', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['name' => 'Alice', 'plays_doubles' => true]);
    $player2 = Player::factory()->for($this->tournament)->create(['name' => 'Bob', 'plays_doubles' => true]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newTeamPlayer1', $player1->id)
        ->set('newTeamPlayer2', $player2->id)
        ->call('createTeam');

    $team = $this->tournament->fresh()->teams->first();

    expect($this->tournament->fresh()->teams)->toHaveCount(1)
        ->and($team->player1_id)->toBe($player1->id)
        ->and($team->player2_id)->toBe($player2->id);
});

it('prevents creating a team from players not marked for doubles', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['plays_doubles' => false]);
    $player2 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newTeamPlayer1', $player1->id)
        ->set('newTeamPlayer2', $player2->id)
        ->call('createTeam');

    expect($this->tournament->fresh()->teams)->toHaveCount(0);
});

it('prevents a player from being in two teams', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $player1 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);
    $player2 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);
    $player3 = Player::factory()->for($this->tournament)->create(['plays_doubles' => true]);

    Team::factory()->for($this->tournament)->create([
        'player1_id' => $player1->id,
        'player2_id' => $player2->id,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->set('newTeamPlayer1', $player1->id) // already teamed
        ->set('newTeamPlayer2', $player3->id)
        ->call('createTeam');

    expect($this->tournament->fresh()->teams)->toHaveCount(1);
});

it('generates a doubles round robin schedule between all teams', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(6)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);
    $teamC = Team::factory()->for($this->tournament)->create(['player1_id' => $players[4]->id, 'player2_id' => $players[5]->id]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('generateDoublesSchedule');

    $doublesGames = $this->tournament->fresh()->games->where('is_doubles', true);

    // 3 teams -> C(3,2) = 3 matches
    expect($doublesGames)->toHaveCount(3)
        ->and($doublesGames->every(fn ($g) => $g->team1_id !== null && $g->team2_id !== null))->toBeTrue();
});

it('calculates doubles standings from completed team matches', function () {
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(4)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);

    $this->tournament->games()->create([
        'team1_id' => $teamA->id,
        'team2_id' => $teamB->id,
        'is_doubles' => true,
        'player1_sets' => 2,
        'player2_sets' => 0,
        'player1_games' => 12,
        'player2_games' => 4,
        'completed' => true,
    ]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament);

    $standings = $component->instance()->doublesStandings;

    expect($standings[0]['team']->id)->toBe($teamA->id)
        ->and($standings[0]['points'])->toBe(2)
        ->and($standings[0]['wins'])->toBe(1)
        ->and($standings[1]['team']->id)->toBe($teamB->id)
        ->and($standings[1]['losses'])->toBe(1);
});

it('auto-creates a doubles final when round robin completes for round_robin_finals', function () {
    $this->actingAs(User::factory()->create());
    $this->tournament->update(['has_doubles' => true, 'doubles_format' => 'round_robin_finals']);

    $players = Player::factory()->count(6)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);
    $teamC = Team::factory()->for($this->tournament)->create(['player1_id' => $players[4]->id, 'player2_id' => $players[5]->id]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('generateDoublesSchedule');

    foreach ($this->tournament->fresh()->games->where('is_doubles', true)->where('is_final', false) as $game) {
        $component->call('updateGameResult', $game->id, 2, 0, 12, 4, [[6, 4], [6, 0]]);
    }

    $final = $this->tournament->fresh()->games->where('is_doubles', true)->where('is_final', true)->first();

    expect($final)->not->toBeNull()
        ->and($final->team1_id)->not->toBeNull()
        ->and($final->team2_id)->not->toBeNull();
});

it('can swap teams in a doubles match', function () {
    $this->tournament->update(['has_doubles' => true]);

    $players = Player::factory()->count(4)->for($this->tournament)->create(['plays_doubles' => true]);
    $teamA = Team::factory()->for($this->tournament)->create(['player1_id' => $players[0]->id, 'player2_id' => $players[1]->id]);
    $teamB = Team::factory()->for($this->tournament)->create(['player1_id' => $players[2]->id, 'player2_id' => $players[3]->id]);

    $game = Game::factory()->for($this->tournament)->create([
        'player1_id' => null,
        'player2_id' => null,
        'team1_id' => $teamA->id,
        'team2_id' => $teamB->id,
        'is_doubles' => true,
        'player1_sets' => 2,
        'player2_sets' => 1,
        'completed' => true,
    ]);

    Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('swapPlayers', $game->id);

    $game->refresh();

    expect($game->team1_id)->toBe($teamB->id)
        ->and($game->team2_id)->toBe($teamA->id)
        ->and($game->player1_sets)->toBe(1)
        ->and($game->player2_sets)->toBe(2);
});

it('excludes doubles-only players from the singles schedule and standings', function () {
    $singles1 = Player::factory()->for($this->tournament)->create(['plays_singles' => true, 'plays_doubles' => false]);
    $singles2 = Player::factory()->for($this->tournament)->create(['plays_singles' => true, 'plays_doubles' => false]);
    $doublesOnly = Player::factory()->for($this->tournament)->create(['plays_singles' => false, 'plays_doubles' => true]);

    $component = Livewire::test(Dashboard::class)
        ->set('tournament', $this->tournament)
        ->call('generateSchedule');

    $games = $this->tournament->fresh()->games;

    // Only the two singles players play -> exactly 1 match, doubles-only player absent.
    expect($games)->toHaveCount(1)
        ->and($games->first()->player1_id)->not->toBe($doublesOnly->id)
        ->and($games->first()->player2_id)->not->toBe($doublesOnly->id);

    $standings = $component->instance()->standings;
    expect(collect($standings)->pluck('player.id'))->not->toContain($doublesOnly->id)
        ->and($standings)->toHaveCount(2);
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
