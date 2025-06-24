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
        // This line will be pre-filled by the --create flag:
        Schema::create('enrollments', function (Blueprint $table) {

            // This primary key is usually added by default:
            $table->id(); // Creates an auto-incrementing primary key column named 'id'

            // Add foreign key for the student (linking to users table)
            $table->foreignId('user_id') // Creates an unsigned big integer column 'user_id'
                  ->constrained()        // Adds foreign key constraint to 'id' on 'users' table
                  ->onDelete('cascade'); // If a user is deleted, their enrollments are also deleted

            // Add foreign key for the course (linking to courses table)
            $table->foreignId('course_id') // Creates an unsigned big integer column 'course_id'
                   ->constrained()         // Adds foreign key constraint to 'id' on 'courses' table
                   ->onDelete('cascade');  // If a course is deleted, enrollments for it are also deleted

            // Optional: Add timestamps if you want to track when enrollment happened
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns

            // IMPORTANT: Prevent duplicate enrollments
            // Ensure a student can only be enrolled in the same course once.
            $table->unique(['user_id', 'course_id']); // Adds a unique constraint across these two columns

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This line will be pre-filled by the --create flag and is correct:
        Schema::dropIfExists('enrollments'); // Deletes the table if it exists when rolling back
    }
};