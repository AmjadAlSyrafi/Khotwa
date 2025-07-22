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

            $table->string('address')->nullable()->after('city');
            $table->string('education_level')->nullable()->change();
            $table->string('university')->nullable()->change();

            $table->json('interests')->nullable()->after('address');
            $table->json('availability')->nullable()->after('interests');
            $table->enum('preferred_time', [
                '1-2 hours per week',
                '3-5 hours per week',
                '6-10 hours per week',
                'more than 10 hours per week'
            ])->nullable()->after('availability');

            $table->integer('volunteering_years')->nullable()->after('preferred_time');
            $table->text('motivation')->nullable()->after('volunteering_years');

            //   الطوارئ
            $table->string('emergency_contact_name')->nullable()->after('motivation');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->enum('emergency_contact_relationship', ['Parent', 'Spouse', 'Friend'])->nullable()->after('emergency_contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'interests',
                'availability',
                'preferred_time',
                'volunteering_years',
                'motivation',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship'
            ]);
        });
    }
};
