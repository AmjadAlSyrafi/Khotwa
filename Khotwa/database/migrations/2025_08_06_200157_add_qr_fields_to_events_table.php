<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('events', function (Blueprint $table) {
        if (!Schema::hasColumn('events', 'qr_token')) {
            $table->string('qr_token')->nullable();
        }

        if (!Schema::hasColumn('events', 'qr_token_expires_at')) {
            $table->timestamp('qr_token_expires_at')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        if (Schema::hasColumn('events', 'qr_token')) {
            $table->dropColumn('qr_token');
        }

        if (Schema::hasColumn('events', 'qr_token_expires_at')) {
            $table->dropColumn('qr_token_expires_at');
        }
    });
}

};
