<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            if (!Schema::hasColumn('badges', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }

            if (!Schema::hasColumn('badges', 'category')) {
                $table->string('category')->after('slug'); // e.g., participation, performance, creativity
            }

            if (!Schema::hasColumn('badges', 'icon')) {
                $table->string('icon')->nullable()->after('category'); // emoji or icon path
            }

            if (!Schema::hasColumn('badges', 'description')) {
                $table->text('description')->after('icon');
            }

            if (!Schema::hasColumn('badges', 'level')) {
                $table->integer('level')->default(1)->after('description');
            }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['slug', 'category', 'icon', 'description', 'level']);
            $table->timestamps();

        });
    }
};
