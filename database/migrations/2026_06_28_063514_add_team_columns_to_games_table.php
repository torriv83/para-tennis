<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('team1_id')->nullable()->after('player2_partner_id')
                ->constrained('teams')->nullOnDelete();
            $table->foreignId('team2_id')->nullable()->after('team1_id')
                ->constrained('teams')->nullOnDelete();
            $table->foreignId('walkover_winner_team_id')->nullable()->after('walkover_winner_id')
                ->constrained('teams')->nullOnDelete();
        });

        $this->migrateExistingDoublesToTeams();
    }

    /**
     * Convert legacy partner-based doubles games into proper team rows.
     */
    protected function migrateExistingDoublesToTeams(): void
    {
        $legacyDoubles = DB::table('games')
            ->where('is_doubles', true)
            ->whereNotNull('player1_partner_id')
            ->whereNotNull('player2_partner_id')
            ->get();

        $resolveTeamId = function (int $tournamentId, int $playerA, int $playerB): int {
            $existing = DB::table('teams')
                ->where('tournament_id', $tournamentId)
                ->where(function ($query) use ($playerA, $playerB) {
                    $query->where(fn ($q) => $q->where('player1_id', $playerA)->where('player2_id', $playerB))
                        ->orWhere(fn ($q) => $q->where('player1_id', $playerB)->where('player2_id', $playerA));
                })
                ->value('id');

            if ($existing) {
                return (int) $existing;
            }

            return DB::table('teams')->insertGetId([
                'tournament_id' => $tournamentId,
                'player1_id' => $playerA,
                'player2_id' => $playerB,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };

        foreach ($legacyDoubles as $game) {
            $team1Id = $resolveTeamId($game->tournament_id, $game->player1_id, $game->player1_partner_id);
            $team2Id = $resolveTeamId($game->tournament_id, $game->player2_id, $game->player2_partner_id);

            $walkoverTeamId = null;
            if ($game->is_walkover && $game->walkover_winner_id) {
                $walkoverTeamId = $game->walkover_winner_id === $game->player1_id ? $team1Id : $team2Id;
            }

            DB::table('games')->where('id', $game->id)->update([
                'team1_id' => $team1Id,
                'team2_id' => $team2Id,
                'walkover_winner_team_id' => $walkoverTeamId,
            ]);

            DB::table('players')
                ->whereIn('id', array_filter([
                    $game->player1_id,
                    $game->player1_partner_id,
                    $game->player2_id,
                    $game->player2_partner_id,
                ]))
                ->update(['plays_doubles' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team1_id');
            $table->dropConstrainedForeignId('team2_id');
            $table->dropConstrainedForeignId('walkover_winner_team_id');
        });
    }
};
