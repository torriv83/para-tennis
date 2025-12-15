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
        if (Schema::hasColumn('tournaments', 'date')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->renameColumn('date', 'start_date');
            });
        }

        if (! Schema::hasColumn('tournaments', 'end_date')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->date('end_date')->nullable()->after('start_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tournaments', 'end_date')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->dropColumn('end_date');
            });
        }

        if (Schema::hasColumn('tournaments', 'start_date')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->renameColumn('start_date', 'date');
            });
        }
    }
};
