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
        Schema::create('user_login_log', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('impersonate_user_id')->unsigned()->nullable();
            $table->enum('status', ['Login', 'Logout']);
            $table->timestamp('activity_time');
            $table->ipAddress('user_ip_address')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('impersonate_user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_log');
    }
};
