<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => \App\Models\Tournament::factory(),
            'player1_id' => \App\Models\Player::factory(),
            'player2_id' => \App\Models\Player::factory(),
            'player1_sets' => 0,
            'player2_sets' => 0,
            'player1_games' => 0,
            'player2_games' => 0,
            'is_final' => false,
            'completed' => false,
            'is_walkover' => false,
        ];
    }

    public function completed(int $player1Sets = 2, int $player2Sets = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'player1_sets' => $player1Sets,
            'player2_sets' => $player2Sets,
            'completed' => true,
        ]);
    }

    public function walkover(int $winnerId): static
    {
        return $this->state(fn (array $attributes) => [
            'is_walkover' => true,
            'walkover_winner_id' => $winnerId,
            'completed' => true,
        ]);
    }

    public function asFinal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => true,
        ]);
    }
}
