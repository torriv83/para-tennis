<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Generate slugs for existing tournaments first
        $tournaments = DB::table('tournaments')->get();
        $slugs = [];

        foreach ($tournaments as $tournament) {
            $slug = Str::slug($tournament->name);
            $originalSlug = $slug;
            $counter = 1;

            while (in_array($slug, $slugs)) {
                $slug = $originalSlug.'-'.$counter++;
            }

            $slugs[$tournament->id] = $slug;
        }

        // Add column with default, then update, then remove default
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('slug')->default('')->after('name');
        });

        foreach ($slugs as $id => $slug) {
            DB::table('tournaments')->where('id', $id)->update(['slug' => $slug]);
        }

        // Add unique index
        Schema::table('tournaments', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
