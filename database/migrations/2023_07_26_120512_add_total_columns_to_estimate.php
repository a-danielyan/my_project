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
        Schema::table('estimate', function (Blueprint $table) {
            $table->unsignedDecimal('sub_total')->nullable();
            $table->unsignedDecimal('total_tax')->nullable();
            $table->unsignedDecimal('total_discount')->nullable();
            $table->unsignedDecimal('grand_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate', function (Blueprint $table) {
            $table->dropColumn(['sub_total', 'total_tax', 'total_discount', 'grand_total']);
        });
    }
};
