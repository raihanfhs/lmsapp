<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_learning_path', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('learning_path_id')
                  ->constrained('learning_paths')
                  ->onDelete('cascade');

            $table->unsignedSmallInteger('order')->default(0); // To define sequence of courses in a path

            $table->timestamps();

            $table->unique(['course_id', 'learning_path_id']); // Prevent adding same course twice to same path
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_learning_path');
    }
};