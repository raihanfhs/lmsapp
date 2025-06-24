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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

             // Link to the student (user) who earned the certificate
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Link to the course the certificate is for
            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->date('issue_date'); // Date the certificate was issued
            $table->string('certificate_path')->nullable(); // Path to a generated PDF file in storage (optional)
            $table->string('unique_code')->unique()->nullable(); // Optional unique verification code

            $table->timestamps();

            // Ensure only one certificate per user per course
            $table->unique(['user_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};