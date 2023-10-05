<?php

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reminder', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('related_entity')->comment('subscription or invoice');
            $table->string('remind_entity')->comment('customer or account or me');
            $table->unsignedSmallInteger('remind_days');
            $table->enum('remind_type', [Reminder::REMIND_TYPE_BEFORE, Reminder::REMIND_TYPE_AFTER]);
            $table->string('condition')->default('due date')->comment('due date or payment date');
            $table->json('sender');
            $table->json('reminder_cc')->nullable();
            $table->json('reminder_bcc')->nullable();
            $table->string('subject');
            $table->text('reminder_text');
            $table->enum('status', [User::STATUS_INACTIVE, User::STATUS_ACTIVE])->default(User::STATUS_ACTIVE);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder');
    }
};
