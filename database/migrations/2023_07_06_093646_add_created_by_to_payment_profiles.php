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
        Schema::table('payment_profile', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_profile', function (Blueprint $table) {
            $table->dropForeign('payment_profile_created_by_foreign');
            $table->dropForeign('payment_profile_updated_by_foreign');
            $table->dropForeign('payment_profile_account_id_foreign');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
