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
        Schema::create('invoice_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('price');
            $table->unsignedDecimal('discount');
            $table->unsignedDecimal('total');
            $table->unsignedDecimal('subtotal');
            $table->unsignedDecimal('tax');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoice');
            $table->foreign('product_id')->references('id')->on('product');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_item');
    }
};
