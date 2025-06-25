<?php
// File migrasi add_details_to_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // Tambah kolom type setelah course_id
            $table->string('type')->default('online')->after('course_id');
            // Tambah kolom location setelah meeting_link
            $table->string('location')->nullable()->after('meeting_link');
            // Ubah meeting_link agar bisa kosong (untuk offline meeting)
            $table->string('meeting_link')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['type', 'location']);
            $table->string('meeting_link')->nullable(false)->change();
        });
    }
};