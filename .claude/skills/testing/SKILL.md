---
name: testing
description: Use when writing tests, creating test files, doing TDD, or when asked to add test coverage. Provides Pest testing patterns and TDD methodology.
---

# Testing Skill (Pest + TDD)

This project uses Pest for testing. Follow TDD methodology: RED -> GREEN -> REFACTOR.

## TDD Cycle

1. **RED**: Write a failing test first
2. **GREEN**: Write minimal code to make it pass
3. **REFACTOR**: Clean up while keeping tests green

## Pest Syntax

### Basic Tests
```php
it('creates a tournament', function () {
    $tournament = Tournament::factory()->create();

    expect($tournament)->toBeInstanceOf(Tournament::class);
});

test('user can view dashboard', function () {
    $this->get('/dashboard')->assertOk();
});
```

### Expectations
```php
expect($value)->toBe($expected);
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();
expect($value)->toBeEmpty();
expect($value)->toContain($item);
expect($value)->toHaveCount(3);
expect($value)->toBeInstanceOf(Model::class);
```

### HTTP Tests
```php
test('can create player', function () {
    $response = $this->post('/players', [
        'name' => 'John Doe',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('players', ['name' => 'John Doe']);
});
```

### Livewire Tests
```php
use Livewire\Livewire;

it('can increment counter', function () {
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1);
});
```

### Using Factories
```php
it('calculates standings correctly', function () {
    $tournament = Tournament::factory()
        ->has(Player::factory()->count(4))
        ->create();

    expect($tournament->players)->toHaveCount(4);
});
```

## Test Organization

- **Feature tests**: Test full request/response cycles, Livewire components
- **Unit tests**: Test isolated classes, services, calculations

## Commands

```bash
# Run all tests
php artisan test

# Run specific file
php artisan test tests/Feature/TournamentTest.php

# Filter by name
php artisan test --filter="calculates standings"

# Run in parallel
php artisan test --parallel
```

## Best Practices

1. One assertion concept per test (can have multiple `expect()` for same concept)
2. Use factories, avoid manual model creation
3. Test happy paths, edge cases, and failure scenarios
4. Name tests descriptively: `it('prevents duplicate player names in tournament')`
5. Keep tests fast - mock external services
