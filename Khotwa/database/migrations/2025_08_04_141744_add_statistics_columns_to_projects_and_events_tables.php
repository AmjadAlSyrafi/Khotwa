<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'total_donations')) {
                $table->decimal('total_donations', 10, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('projects', 'total_volunteers')) {
                $table->integer('total_volunteers')->default(0)->after('total_donations');
            }
            if (!Schema::hasColumn('projects', 'total_events')) {
                $table->integer('total_events')->default(0)->after('total_volunteers');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'required_volunteers')) {
                $table->integer('required_volunteers')->default(0)->after('status');
            }
            if (!Schema::hasColumn('events', 'current_volunteers')) {
                $table->integer('current_volunteers')->default(0)->after('required_volunteers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['total_donations', 'total_volunteers', 'total_events']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['required_volunteers', 'current_volunteers']);
        });
    }
};
