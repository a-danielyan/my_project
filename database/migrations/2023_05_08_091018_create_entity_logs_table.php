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
        Schema::create('entity_log', function (Blueprint $table) {
            $table->id();
            $table->string('entity')->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->unsignedBigInteger('field_id');
            $table->string('previous_value');
            $table->string('new_value');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('update_id')->comment(
                'We use his field for understanding what data changed  together in specific request',
            );
            $table->timestamps();
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_log');
    }
};
