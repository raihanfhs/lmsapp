<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the pivot table to link courses and teachers (users).
     */
    public function up(): void
    {
        // Convention for pivot table names is singular tables, alphabetical order
        Schema::create('course_teacher', function (Blueprint $table) {
            $table->id(); // Optional primary key for the pivot record itself

            // Foreign key for the course
            $table->foreignId('course_id')
                  ->constrained()        // Links to 'id' on 'courses' table
                  ->onDelete('cascade'); // If course deleted, remove assignment

            // Foreign key for the teacher (user)
            $table->foreignId('user_id')
                  ->constrained()        // Links to 'id' on 'users' table
                  ->onDelete('cascade'); // If teacher user deleted, remove assignment

            // Optional: track when the assignment was made
            $table->timestamps();

            // Prevent assigning the same teacher to the same course multiple times
            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_teacher');
    }
};