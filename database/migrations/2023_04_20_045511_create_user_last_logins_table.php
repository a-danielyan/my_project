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
        Schema::create('user_last_login', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('impersonate_user_id')->unsigned()->nullable();
            $table->foreign('impersonate_user_id')->references('id')->on('users');
            $table->timestamp('login_at');
            $table->timestamp('activity_time');
            $table->timestamp('logout_at');
            $table->ipAddress('user_ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_last_login');
    }
};
