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
        Schema::create('public_link_access_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publish_detail_id');
            $table->string('ip');
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->foreign('publish_detail_id')->references('id')
                ->on('publish_details')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_link_access_log');
    }
};
