<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'player1_id',
        'player2_id',
        'scheduled_at',
        'player1_sets',
        'player2_sets',
        'player1_games',
        'player2_games',
        'set_scores',
        'is_final',
        'completed',
        'is_walkover',
        'walkover_winner_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'is_final' => 'boolean',
            'completed' => 'boolean',
            'is_walkover' => 'boolean',
            'set_scores' => 'array',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player1(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    public function walkoverWinner(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'walkover_winner_id');
    }

    public function winner(): ?Player
    {
        if (! $this->completed) {
            return null;
        }

        if ($this->is_walkover) {
            return $this->walkoverWinner;
        }

        return $this->player1_sets > $this->player2_sets
            ? $this->player1
            : $this->player2;
    }

    public function loser(): ?Player
    {
        if (! $this->completed) {
            return null;
        }

        if ($this->is_walkover) {
            return $this->walkover_winner_id === $this->player1_id
                ? $this->player2
                : $this->player1;
        }

        return $this->player1_sets < $this->player2_sets
            ? $this->player1
            : $this->player2;
    }
}
