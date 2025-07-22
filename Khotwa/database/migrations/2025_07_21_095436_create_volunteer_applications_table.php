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
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();

            // Personal Information
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('study')->nullable();
            $table->string('career')->nullable();
            $table->string('city');
            $table->string('address')->nullable();

            // Volunteer Information
            $table->json('interests');
            $table->json('availability');
            $table->enum('preferred_time', [
                '1-2 hours per week',
                '3-5 hours per week',
                '6-10 hours per week',
                'more than 10 hours per week'
            ]);

            // Experience & Skills
            $table->integer('volunteering_years')->nullable();
            $table->json('skills')->nullable();
            $table->text('motivation')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->enum('emergency_contact_relationship', ['Parent', 'Spouse', 'Friend']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
