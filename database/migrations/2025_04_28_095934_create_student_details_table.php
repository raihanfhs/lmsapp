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
        Schema::create('student_details', function (Blueprint $table) {
            $table->id(); // Primary key for this table

            // Foreign key linking to the users table
            $table->foreignId('user_id')
                  ->constrained()        // Links to 'id' on 'users' table
                  ->onDelete('cascade'); // If the user is deleted, delete this detail record too

            // Student-specific fields
            $table->string('student_id_number')->unique()->nullable(); // Unique student ID, nullable allows creation before ID is known
            $table->date('enrollment_date')->nullable(); // Date student enrolled, nullable
            $table->string('major')->nullable(); // Student's major, nullable

            $table->timestamps(); // 'created_at' and 'updated_at' timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_details'); // Correctly drops the table on rollback
    }
};