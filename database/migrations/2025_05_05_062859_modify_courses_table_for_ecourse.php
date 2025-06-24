<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Removes user_id (creator) and adds e-course specific fields.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Simpler approach: Try dropping foreign key assuming default name,
            // then drop column. Might fail if key name was custom.
            if (Schema::hasColumn('courses', 'user_id')) {
                try {
                    // Laravel's default convention is tablename_columnname_foreign
                    $table->dropForeign('courses_user_id_foreign');
                     // Or if just passing the column name worked before:
                     // $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                     logger("Could not drop foreign key for user_id before dropping column. Error: " . $e->getMessage());
                     // Continue anyway, hoping the column drop works
                }
                $table->dropColumn('user_id');
            }
    
            // Add new columns (same as before)
            $table->integer('duration_months')->nullable()->after('course_code')->comment('Estimated duration in months');
            $table->dateTime('final_exam_date')->nullable()->after('duration_months');
            $table->unsignedTinyInteger('passing_grade')->nullable()->after('final_exam_date')->comment('Passing grade percentage (0-100)');
            $table->string('certificate_template_path')->nullable()->after('passing_grade')->comment('Path to certificate template if any');
        });
    }

    /**
     * Reverse the migrations.
     * Removes e-course fields and adds back the user_id creator column.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the new columns (in reverse order or as array)
            $table->dropColumn([
                'duration_months',
                'final_exam_date',
                'passing_grade',
                'certificate_template_path'
            ]);

            // Add the user_id column back (assuming it was non-nullable bigint unsigned originally)
            // Place it after 'id' for consistency with original likely structure
             if (!Schema::hasColumn('courses', 'user_id')) {
                 $table->foreignId('user_id')->after('id'); // Adjust position if needed based on original migration

                 // Add the foreign key constraint back
                 $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
             }
        });
    }
};