<?php

namespace App\Services;

use App\Models\Tournament;
use Illuminate\Support\Facades\Auth;

class PinAuthService
{
    private const SESSION_KEY = 'tournament_pin_access';

    public function authenticate(Tournament $tournament, string $pin): bool
    {
        if (! $tournament->verifyPin($pin)) {
            return false;
        }

        $access = session(self::SESSION_KEY, []);
        $access[$tournament->id] = true;
        session([self::SESSION_KEY => $access]);

        return true;
    }

    public function logout(Tournament $tournament): void
    {
        $access = session(self::SESSION_KEY, []);
        unset($access[$tournament->id]);
        session([self::SESSION_KEY => $access]);
    }

    public function hasAccess(Tournament $tournament): bool
    {
        $access = session(self::SESSION_KEY, []);

        if (! isset($access[$tournament->id])) {
            return false;
        }

        return $tournament->isPinValid();
    }

    public function canEditResults(Tournament $tournament): bool
    {
        if (Auth::check()) {
            return true;
        }

        return $this->hasAccess($tournament);
    }
}
