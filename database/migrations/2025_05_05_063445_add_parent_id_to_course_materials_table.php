<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds parent_id for material hierarchy.
     */
    public function up(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Add the parent_id column after course_id (or choose desired position)
            $table->foreignId('parent_id')
                  ->nullable() // Allow NULL for top-level materials
                  ->after('course_id') // Optional: places column after course_id
                  ->constrained('course_materials') // Foreign key to SAME table (id column)
                  ->onDelete('cascade'); // If a parent material is deleted, delete its children
        });
    }

    /**
     * Reverse the migrations.
     * Removes the parent_id column and its constraint.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
             // Drop foreign key first (using default constraint name convention)
             // Convention: tablename_columnname_foreign -> course_materials_parent_id_foreign
             if (Schema::hasColumn('course_materials', 'parent_id')) { // Check if column exists
                try {
                    // Attempt to drop using default convention first
                    $table->dropForeign(['parent_id']);
                } catch (\Exception $e) {
                    // Log or handle error if needed, maybe constraint name is different
                    // For now, we proceed to dropping the column anyway if dropping constraint fails
                    logger('Could not drop parent_id foreign key automatically in migration rollback: ' . $e->getMessage());
                }
                $table->dropColumn('parent_id');
            }
        });
    }
};
