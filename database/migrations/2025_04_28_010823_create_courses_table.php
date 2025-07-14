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
            // This schema matches the final state of your table
            $table->string('title');
            $table->text('description');
            $table->string('course_code')->unique();
            $table->integer('duration_months')->nullable()->comment('Estimated duration in months');
            $table->dateTime('final_exam_date')->nullable();
            $table->unsignedTinyInteger('passing_grade')->nullable()->comment('Passing grade percentage (0-100)');
            $table->string('certificate_template_path')->nullable()->comment('Path to certificate template if any');
            $table->timestamps();
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