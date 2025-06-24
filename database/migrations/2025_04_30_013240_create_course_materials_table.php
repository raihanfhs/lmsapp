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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id(); // Primary key for the material record

            // Foreign key to link this material to a specific course
            $table->foreignId('course_id')
                  ->constrained()        // Links to 'id' on 'courses' table
                  ->onDelete('cascade'); // If course is deleted, delete associated materials

            $table->string('title'); // Title of the video or material (e.g., "Lecture 1: Introduction")
            $table->text('description')->nullable(); // Optional description for the material
            $table->string('file_path'); // Stores the path to the uploaded file in storage (e.g., 'course_videos/1/xyz.mp4')
            $table->string('file_type')->default('video'); // Type of file (default to video, could be 'pdf', etc. later)
            $table->integer('order')->default(0); // Optional: For controlling display order within a course

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials'); // Correctly drops the table on rollback
    }
};