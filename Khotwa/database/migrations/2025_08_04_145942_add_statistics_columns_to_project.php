<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'target_donation')) {
                $table->decimal('target_donation', 10, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('projects', 'donated_amount')) {
                $table->decimal('donated_amount', 10, 2)->default(0)->after('target_donation');
            }
            if (!Schema::hasColumn('projects', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('donated_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['target_donation', 'donated_amount', 'remaining_amount']);
        });
    }
};
