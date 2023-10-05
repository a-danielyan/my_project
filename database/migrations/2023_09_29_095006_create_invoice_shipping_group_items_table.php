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
        Schema::create('invoice_shipping_group_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->json('address');
            $table->timestamps();
            $table->foreign('contact_id')->references('id')->on('contact')->cascadeOnDelete();
            $table->foreign('invoice_id')->references('id')->on('invoice')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_shipping_group_item');
    }
};
