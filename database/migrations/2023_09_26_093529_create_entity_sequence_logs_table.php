<?php

use App\Models\Sequence\SequenceEntityLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sequence_entity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('email_template_id');
            $table->unsignedBigInteger('sequence_id');
            $table->enum(
                'status',
                [SequenceEntityLog::STATUS_NEW, SequenceEntityLog::STATUS_SENT, SequenceEntityLog::STATUS_ERROR],
            );
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->foreign('entity_id')->references('id')->on('sequence_entity_associations')->cascadeOnDelete();
            $table->foreign('email_template_id')->references('id')->on(
                'sequence_template_associations',
            )->cascadeOnDelete();
            $table->foreign('sequence_id')->references('id')->on('sequence')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_entity_logs');
    }
};
