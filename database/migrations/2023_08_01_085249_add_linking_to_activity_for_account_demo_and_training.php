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
        Schema::table('account_demo', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->foreign('activity_id')->references('id')->on('activity');
        });

        Schema::table('account_training', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->foreign('activity_id')->references('id')->on('activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_demo', function (Blueprint $table) {
            $table->dropForeign('account_demo_activity_id_foreign');
            $table->dropColumn('activity_id');
        });

        Schema::table('account_training', function (Blueprint $table) {
            $table->dropForeign('account_training_activity_id_foreign');
            $table->dropColumn('activity_id');
        });
    }
};
