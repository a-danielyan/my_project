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
        Schema::table('estimate_item', function (Blueprint $table) {
            $table->dropConstrainedForeignId('estimate_id');
            $table->dropColumn('price');
            $table->decimal('tax_percent')->default(0);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('estimate_shipping_group_item')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate_item', function (Blueprint $table) {
            //
        });
    }
};
