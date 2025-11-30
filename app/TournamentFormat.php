<?php

namespace App;

enum TournamentFormat: string
{
    case RoundRobin = 'round_robin';
    case RoundRobinFinals = 'round_robin_finals';

    public function label(): string
    {
        return match ($this) {
            self::RoundRobin => 'Round Robin',
            self::RoundRobinFinals => 'Round Robin + Finals',
        };
    }
}
