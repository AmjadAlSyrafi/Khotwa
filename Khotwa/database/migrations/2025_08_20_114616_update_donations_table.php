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
        Schema::table('donations', function (Blueprint $table) {
        if (!Schema::hasColumn('donations', 'updated_at')) {
            $table->string('updated_at')->nullable();
        }
        if (!Schema::hasColumn('donations', 'created_at')) {
            $table->string('created_at')->nullable();
        }

    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
        if (!Schema::hasColumn('donations', 'updated_at')) {
            $table->dropColumn('updated_at');
        }
        if (!Schema::hasColumn('donations', 'created_at')) {
            $table->dropColumn('created_at');
        }

    });

    }
};
