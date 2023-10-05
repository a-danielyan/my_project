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
        Schema::table('payment', function (Blueprint $table) {
            $table->dropColumn('balance_due');
        });
        Schema::table('invoice', function (Blueprint $table) {
            $table->unsignedDecimal('balance_due')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->unsignedDecimal('balance_due')->default(0);
        });
        Schema::table('invoice', function (Blueprint $table) {
            $table->dropColumn('balance_due');
        });
    }
};
