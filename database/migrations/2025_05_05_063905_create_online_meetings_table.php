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
        Schema::create('online_meetings', function (Blueprint $table) {
            $table->id();

            // Link to the course this meeting belongs to
            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Optional: Link to the teacher who scheduled/hosts the meeting
            $table->foreignId('teacher_id') // Column name assumes it links to users table
                  ->nullable()
                  ->constrained('users') // Explicitly link to users table
                  ->nullOnDelete(); // If teacher account deleted, keep meeting record but nullify teacher_id

            $table->string('title'); // e.g., "Week 1 - Q&A Session"
            $table->text('description')->nullable(); // Optional details
            $table->dateTime('meeting_datetime'); // Date and time of the meeting
            $table->string('meeting_link', 1000); // URL for Zoom, GMeet, etc. Increased length.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_meetings');
    }
};