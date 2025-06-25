<?php
// File migrasi rename_online_meetings_to_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('online_meetings', 'meetings');
    }

    public function down(): void
    {
        Schema::rename('meetings', 'online_meetings');
    }
};