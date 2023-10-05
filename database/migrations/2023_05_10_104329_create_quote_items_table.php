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
        Schema::create('estimate_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('price');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreign('estimate_id')->references('id')->on('estimate');
            $table->foreign('product_id')->references('id')->on('product');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_item');
    }
};
