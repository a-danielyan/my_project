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
        Schema::create('sequence_entity_associations', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->unsignedTinyInteger('count_emails_sent')->default(0);
            $table->unsignedBigInteger('sequence_id');
            $table->timestamps();
            $table->foreign('sequence_id')->references('id')->on('sequence')->cascadeOnDelete();
            $table->unique(['entity_id', 'entity_type', 'sequence_id'], 'uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_entity_associations');
    }
};
