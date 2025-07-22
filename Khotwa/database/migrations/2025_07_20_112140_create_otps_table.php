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
    Schema::create('otps', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->unsignedMediumInteger('code');
        $table->timestamp('expires_at');
        $table->timestamps(); 

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('otps');
}
};
