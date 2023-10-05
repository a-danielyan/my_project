<?php

use App\Models\ActivityReminder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_reminder', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->enum('reminder_type', ActivityReminder::AVAILABLE_REMINDER_TYPE);
            $table->unsignedInteger('reminder_time')->nullable();
            $table->enum('reminder_unit', ActivityReminder::AVAILABLE_REMINDER_UNITS)->nullable();
            $table->foreign('activity_id')->references('id')->on('activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_reminder');
    }
};
