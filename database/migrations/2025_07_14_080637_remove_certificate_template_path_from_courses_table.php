<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Logika untuk MENGHAPUS kolom
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('certificate_template_path');
        });
    }

    public function down(): void
    {
        // Logika untuk MENGEMBALIKAN kolom jika terjadi rollback
        Schema::table('courses', function (Blueprint $table) {
            $table->string('certificate_template_path')->nullable()->after('passing_grade');
        });
    }
};