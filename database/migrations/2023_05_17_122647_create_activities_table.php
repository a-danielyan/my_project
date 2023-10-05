<?php

use App\Models\Activity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('related_to');
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
            $table->enum('activity_type', Activity::ACTIVITY_TYPES);
            $table->enum('activity_status', Activity::ACTIVITY_STATUSES);
            $table->enum('priority', Activity::PRIORITY_STATUSES)->default(Activity::PRIORITY_NORMAL_STATUS);
            $table->date('due_date')->nullable();
            $table->string('subject');
            $table->string('related_to_entity');
            $table->unsignedBigInteger('related_to_id');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('reminder_at')->nullable();
            $table->string('reminder_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('related_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity');
    }
};
