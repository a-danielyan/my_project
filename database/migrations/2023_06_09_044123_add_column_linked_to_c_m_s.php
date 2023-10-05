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
        Schema::table('account', function (Blueprint $table) {
            $table->unsignedBigInteger('cms_client_id')->nullable();
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->unsignedBigInteger('cms_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('cms_client_id');
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('cms_user_id');
        });
    }
};
