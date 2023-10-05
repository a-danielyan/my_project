<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_field_option', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('sort_order')->nullable();
            $table->unsignedBigInteger('custom_field_id');
            $table->foreign('custom_field_id')->references('id')->on('custom_field')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_field_option');
    }
};
