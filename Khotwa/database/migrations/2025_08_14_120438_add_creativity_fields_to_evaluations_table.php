<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('evaluations', function (Blueprint $table) {
        $table->boolean('initiated')->default(false); // اقترح فكرة أو مبادرة
        $table->boolean('mentored')->default(false); // دعم أو تدريب متطوعين
        $table->boolean('creative_contribution')->default(false); // إبداع أو تصميم
        $table->boolean('impactful')->default(false); // أثر في الفعالية
        $table->boolean('inspirational')->default(false); // إلهام الفريق
    });
}

public function down()
{
    Schema::table('evaluations', function (Blueprint $table) {
        $table->dropColumn([
            'initiated',
            'mentored',
            'creative_contribution',
            'impactful',
            'inspirational'
        ]);
    });
}

};
