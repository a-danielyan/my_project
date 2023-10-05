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
        Schema::table('invoice_item', function (Blueprint $table) {
            $table->decimal('quantity')->change();
            $table->decimal('price')->change();
            $table->decimal('discount')->change();
            $table->decimal('total')->change();
            $table->decimal('subtotal')->change();
            $table->decimal('tax')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_item', function (Blueprint $table) {
            $table->unsignedDecimal('quantity')->change();
            $table->unsignedDecimal('price')->change();
            $table->unsignedDecimal('discount')->change();
            $table->unsignedDecimal('total')->change();
            $table->unsignedDecimal('subtotal')->change();
            $table->unsignedDecimal('tax')->change();
        });
    }
};
