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
        Schema::create('skill_user', function (Blueprint $table) {
            // Using unsignedBigInteger for foreign keys is common practice
            // if your primary keys on users and skills are bigIncrements.
            // If they are just increments (integer), use unsignedInteger.
            // Laravel's foreignId() handles this automatically.

            $table->foreignId('user_id')
                  ->constrained()         // Assumes 'users' table and 'id' column
                  ->onDelete('cascade'); // If user deleted, remove skill association

            $table->foreignId('skill_id')
                  ->constrained()         // Assumes 'skills' table and 'id' column
                  ->onDelete('cascade'); // If skill deleted, remove association

            // Define a composite primary key to ensure unique user-skill pairs
            // and to allow efficient indexing.
            $table->primary(['user_id', 'skill_id']);

            // Timestamps are usually not needed on a simple pivot table like this
            // unless you want to track when a skill was assigned.
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_user');
    }
};