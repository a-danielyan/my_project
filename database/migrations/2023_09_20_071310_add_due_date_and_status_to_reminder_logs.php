<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reminder_log', function (Blueprint $table) {
            $table->date('reminder_date');
            $table->string('status');
            $table->text('error')->nullable();
            $table->unsignedBigInteger('sent_entity_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_log', function (Blueprint $table) {
            $table->dropColumn(['reminder_date', 'status', 'error']);
        });
    }
};
