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

            $table->decimal('amount', 10, 2)->change();
            $table->text('description')->nullable()->change();
            $table->string('donor_name', 100)->nullable()->change();
            $table->string('method', 100)->default('manual')->change();
            $table->string('transaction_id', 255)->nullable()->change();    


            $table->string('donor_email')->nullable()->after('donor_name');
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending')->after('transaction_id');
            $table->integer('event_id')->nullable()->after('project_id');
            $table->timestamp('donated_at')->useCurrent()->after('event_id');

            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {

            $table->decimal('amount', 10, 2)->change();
            $table->string('description')->nullable()->change();
            $table->string('donor_name')->nullable()->change();
            $table->string('method', 100)->default('manual')->change();
            $table->string('transaction_id')->nullable()->change();

            $table->dropColumn(['donor_email', 'payment_status', 'event_id', 'donated_at']);
        });
    }
};
