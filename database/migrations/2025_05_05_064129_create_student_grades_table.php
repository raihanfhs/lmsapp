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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();

            // Link to the student (user)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Link to the course the grade is for
            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Could add exam_id later if implementing multiple quizzes/exams
            // For now, assumes one main grade per course for certificate logic

            $table->decimal('grade', 5, 2)->nullable(); // e.g., Stores grades like 95.50, 80.00 (up to 999.99)
            $table->boolean('passed')->nullable(); // Flag set based on course passing_grade
            $table->dateTime('attempt_datetime')->nullable(); // When the grade was recorded

            $table->timestamps();

            // Optional: Prevent multiple final grades per user/course if needed
            // $table->unique(['user_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};