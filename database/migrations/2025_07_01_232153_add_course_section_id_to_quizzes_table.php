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
            // Add course_section_id column
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->onDelete('set null')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop foreign key and column if rolling back
            $table->dropForeign(['course_section_id']);
            $table->dropColumn('course_section_id');
        });
    }
};