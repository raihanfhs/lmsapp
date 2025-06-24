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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            // Make sure this line exists and is not commented out or misspelled:
            $table->foreignId('user_id'); // Creates the user_id column
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('course_code')->unique()->nullable();
            $table->timestamps();
    
            // This adds the constraint, but the line above creates the column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
