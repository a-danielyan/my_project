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
        Schema::create('email_to_entity_associations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_id');
            $table->unsignedBigInteger('entity_id');
            $table->string('entity');
            $table->foreign('email_id')->references('id')->on('email');

            $table->unique(['email_id', 'entity_id', 'entity'], 'uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_to_entity_associations');
    }
};
