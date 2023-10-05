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
        Schema::table('opportunity_stage_log', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->unsignedBigInteger('stage_id')->nullable()->change();
            $table->foreign('stage_id')->references('id')->on('stage')->nullOnDelete();
        });

        Schema::table('opportunity', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->unsignedBigInteger('stage_id')->nullable()->change();
            $table->foreign('stage_id')->references('id')->on('stage')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
