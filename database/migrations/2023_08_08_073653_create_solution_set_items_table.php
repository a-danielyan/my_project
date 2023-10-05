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
        Schema::create('solution_set_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solution_set_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('price');
            $table->string('description')->nullable();
            $table->unsignedDecimal('discount')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('solution_set_id')->references('id')->on('solution_set')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solution_set_item');
    }
};
