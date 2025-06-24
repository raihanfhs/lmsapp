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
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id(); // Primary key for this table

            // Foreign key linking to the users table
            $table->foreignId('user_id')
                  ->constrained()        // Links to 'id' on 'users' table
                  ->onDelete('cascade'); // If the user is deleted, delete this detail record too

            // Teacher-specific fields
            $table->string('employee_id_number')->unique()->nullable(); // Unique employee ID, nullable
            $table->string('qualification')->nullable(); // e.g., PhD, MSc, etc., nullable
            $table->string('department')->nullable(); // e.g., Computer Science, History, nullable

            $table->timestamps(); // 'created_at' and 'updated_at' timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_details'); // Correctly drops the table on rollback
    }
};