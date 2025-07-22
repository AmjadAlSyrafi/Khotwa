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
        Schema::table('volunteers', function (Blueprint $table) {
            $table->json('availability_days')->nullable();
            $table->enum('preferred_time', [
                '1-2 hours per week',
                '3-5 hours per week',
                '6-10 hours per week',
                'more than 10 hours per week'
            ]);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'volunteering_years', 'motivation',
                'availability_days', 'preferred_time',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation'
            ]);
        });
    }

};
