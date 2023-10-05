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
        Schema::table('oauth_token', function (Blueprint $table) {
            $table->string('user_name')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('redirect_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_token', function (Blueprint $table) {
            $table->dropColumn([
                'user_name',
                'client_id',
                'client_secret',
                'expires_in',
                'redirect_url',
            ]);
        });
    }
};
