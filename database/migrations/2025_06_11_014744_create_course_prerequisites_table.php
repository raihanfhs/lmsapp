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
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();

            // This is the course that HAS prerequisites (e.g., Advanced Python)
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->onDelete('cascade');

            // This is the course that IS the prerequisite (e.g., Beginner Python)
            $table->foreignId('prerequisite_id')
                  ->constrained('courses') // Also links to the courses table
                  ->onDelete('cascade');

            // Optional: track when the prerequisite was set
            $table->timestamps();

            // Prevent setting the same prerequisite twice for the same course
            $table->unique(['course_id', 'prerequisite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};