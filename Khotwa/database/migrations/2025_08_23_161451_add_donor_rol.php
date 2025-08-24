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
        DB::statement("
            ALTER TABLE `roles`
            CHANGE `name` `name`
            ENUM('Admin', 'Supervisor', 'Volunteer', 'Donor')
            NOT NULL DEFAULT 'Volunteer'
        ");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `roles`
            CHANGE `name` `name`
            ENUM('Admin', 'Supervisor', 'Volunteer')
            NOT NULL DEFAULT 'Volunteer'
        ");
    }
};
