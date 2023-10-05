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
        Schema::create('email', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('token_id');
            $table->string('email_id')->unique();
            $table->timestamp('received_date');
            $table->string('from')->nullable();
            $table->json('to')->nullable();
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('token_id')->references('id')->on('oauth_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email');
    }
};
