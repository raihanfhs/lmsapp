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
        Schema::table('course_materials', function (Blueprint $table) {
            // Add the parent_id column as a nullable foreign key
            $table->foreignId('parent_id')
                  ->nullable() // Allow nulls for top-level materials
                  ->constrained('course_materials') // Constrain to the same table
                  ->onDelete('cascade') // Delete children if parent is deleted
                  ->after('order'); // Place it after the 'order' column (optional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropConstrainedForeignId('parent_id');
            // Then drop the column
            $table->dropColumn('parent_id');
        });
    }
};