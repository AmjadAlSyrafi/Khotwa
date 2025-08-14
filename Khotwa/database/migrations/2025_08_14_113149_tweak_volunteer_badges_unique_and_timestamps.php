<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('volunteer_badges', function (Blueprint $table) {
            // Add timestamps if missing
            if (!Schema::hasColumn('volunteer_badges', 'created_at')) {
                $table->timestamps();
            }

            // Add unique index to prevent duplicates (volunteer_id, badge_id)
            $table->unique(['volunteer_id', 'badge_id'], 'volunteer_badges_unique');
        });
    }

    public function down(): void {
        Schema::table('volunteer_badges', function (Blueprint $table) {
            $table->dropUnique('volunteer_badges_unique');
            // keep timestamps; dropping them is optional
        });
    }
};
