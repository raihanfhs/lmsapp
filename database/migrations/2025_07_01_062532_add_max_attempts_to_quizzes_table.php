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
        Schema::table('quizzes', function (Blueprint $table) {
            // Add the new max_attempts column
            // We will make it an unsigned tiny integer, as it's unlikely to be a large number.
            // It's nullable and defaults to null, meaning "unlimited attempts" if not set.
            $table->unsignedTinyInteger('max_attempts')->nullable()->default(null)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // This will remove the column if we need to roll back the migration.
            $table->dropColumn('max_attempts');
        });
    }
};