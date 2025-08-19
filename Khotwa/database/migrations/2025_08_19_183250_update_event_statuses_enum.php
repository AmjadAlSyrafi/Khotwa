<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `events` CHANGE `status` `status` ENUM('completed', 'upcoming', 'open', 'closed') NOT NULL DEFAULT 'upcoming'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `events` CHANGE `status` `status` ENUM('open', 'closed', 'completed') NOT NULL DEFAULT 'upcoming'");
    }
};
