<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {

            if (!Schema::hasColumn('evaluations', 'punctuality'))   $table->tinyInteger('punctuality')->unsigned()->nullable()->after('supervisor_id');
            if (!Schema::hasColumn('evaluations', 'work_quality'))  $table->tinyInteger('work_quality')->unsigned()->nullable()->after('punctuality');
            if (!Schema::hasColumn('evaluations', 'teamwork'))      $table->tinyInteger('teamwork')->unsigned()->nullable()->after('work_quality');
            if (!Schema::hasColumn('evaluations', 'initiative'))    $table->tinyInteger('initiative')->unsigned()->nullable()->after('teamwork');
            if (!Schema::hasColumn('evaluations', 'discipline'))    $table->tinyInteger('discipline')->unsigned()->nullable()->after('initiative');

            if (!Schema::hasColumn('evaluations', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->nullable()->after('discipline');
            }

            if (!Schema::hasColumn('evaluations', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }

            if (!Schema::hasColumn('evaluations', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->unique(['volunteer_id', 'event_id', 'supervisor_id'], 'eval_unique_triplet');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('evaluations', 'punctuality'))   $table->dropColumn('punctuality');
            if (Schema::hasColumn('evaluations', 'work_quality'))  $table->dropColumn('work_quality');
            if (Schema::hasColumn('evaluations', 'teamwork'))      $table->dropColumn('teamwork');
            if (Schema::hasColumn('evaluations', 'initiative'))    $table->dropColumn('initiative');
            if (Schema::hasColumn('evaluations', 'discipline'))    $table->dropColumn('discipline');
            if (Schema::hasColumn('evaluations', 'average_rating')) $table->dropColumn('average_rating');

            if (Schema::hasColumn('evaluations', 'notes')) $table->dropColumn('notes');

            $table->dropUnique('eval_unique_triplet');
        });
    }
};
