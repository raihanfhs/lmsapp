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
        // Get the existing 'profiles' table to add columns
        Schema::table('profiles', function (Blueprint $table) {
            // Add the new address columns, making them nullable
            // You can use ->after('column_name') to place them nicely in the table structure
            $table->string('address_line_1')->nullable()->after('phone_number');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city'); // state or province
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the 'profiles' table to remove the columns if we roll back
        Schema::table('profiles', function (Blueprint $table) {
            // Drop columns in reverse order (or provide an array)
            $table->dropColumn([
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postal_code',
                'country'
            ]);
        });
    }
};