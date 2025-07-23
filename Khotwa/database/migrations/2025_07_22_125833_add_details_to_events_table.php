<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->integer('required_volunteers')->default(0)->after('status');
            $table->integer('registered_count')->default(0)->after('required_volunteers');
            $table->timestamps(); // Adds created_at and updated_at

        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['required_volunteers', 'registered_count']);
            $table->dropTimestamps();

        });
    }
};
