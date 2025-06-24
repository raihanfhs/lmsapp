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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')
                  ->nullable() // Important: Allows existing users (and Students) to not have a division
                  ->after('email_verified_at') // Or choose another suitable position
                  ->constrained('divisions')     // Foreign key to the 'id' column on 'divisions' table
                  ->nullOnDelete();     // If a division is deleted, set division_id for users in that division to NULL
                                        // Alternatively, you could use ->restrictOnDelete(); to prevent division deletion if users are assigned.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_id')) { // Check before dropping
                // Convention for foreign key name: tablename_columnname_foreign -> users_division_id_foreign
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
        });
    }
};