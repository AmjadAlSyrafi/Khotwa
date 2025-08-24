<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteers', 'total_volunteer_hours')) {
                $table->integer('total_volunteer_hours')->default(0);
            }

            $table->json('availability_days')->nullable()->after('availability');

        });

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'volunteer_hours')) {
                $table->integer('volunteer_hours')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (Schema::hasColumn('volunteers', 'total_volunteer_hours')) {
                $table->dropColumn('total_volunteer_hours');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'volunteer_hours')) {
                $table->dropColumn('volunteer_hours');
            }
        });
    }
};
