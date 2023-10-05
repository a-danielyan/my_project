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
        Schema::create('mailgun_notification_raw_data', function (Blueprint $table) {
            $table->id();
            $table->mediumText('raw_data')->nullable();
            $table->enum('processing_status', ['NEW', 'DONE', 'ERROR'])->default('NEW');
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailgun_notification_raw_data');
    }
};
