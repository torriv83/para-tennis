<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->boolean('has_doubles')->default(false)->after('format');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('player1_partner_id')->nullable()->after('player1_id')
                ->constrained('players')->nullOnDelete();
            $table->foreignId('player2_partner_id')->nullable()->after('player2_id')
                ->constrained('players')->nullOnDelete();
            $table->boolean('is_doubles')->default(false)->after('is_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('player1_partner_id');
            $table->dropConstrainedForeignId('player2_partner_id');
            $table->dropColumn('is_doubles');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('has_doubles');
        });
    }
};
