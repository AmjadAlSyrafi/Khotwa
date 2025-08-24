<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->string('profile_image', 255)->nullable()->after('total_volunteer_hours');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('cover_image', 255)->nullable()->after('status');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('cover_image', 255)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
