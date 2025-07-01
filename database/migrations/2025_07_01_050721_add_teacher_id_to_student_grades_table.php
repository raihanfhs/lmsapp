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
        Schema::table('student_grades', function (Blueprint $table) {
            // Add the new teacher_id column
            $table->foreignId('teacher_id')
                  ->nullable() // Make it nullable in case a grade can be system-generated
                  ->after('course_id') // Place it after the course_id column
                  ->constrained('users') // Foreign key to the 'id' on the 'users' table
                  ->onDelete('set null'); // If teacher is deleted, keep the grade but set teacher_id to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            // Drop the foreign key constraint first, then the column
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};