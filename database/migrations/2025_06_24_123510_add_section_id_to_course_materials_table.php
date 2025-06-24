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
            // Add the new column for the section relationship
            $table->foreignId('course_section_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');

            // !!----- FIX: DROP THE FOREIGN KEY CONSTRAINT FIRST -----!!
            $table->dropForeign(['parent_id']);

            // Now that the constraint is gone, we can safely drop the column
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropForeign(['course_section_id']);
            $table->dropColumn('course_section_id');
            
            // Re-create the column
            $table->unsignedBigInteger('parent_id')->nullable()->after('course_id');
            // Re-create the foreign key constraint
            $table->foreign('parent_id')->references('id')->on('course_materials')->onDelete('cascade');
        });
    }
};