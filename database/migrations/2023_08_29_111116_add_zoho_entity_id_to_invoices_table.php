<?php

use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id_sales_order')->nullable();
            $table->unsignedBigInteger('zoho_entity_id_invoice')->nullable();
            $table->string('payment_term')->default(Invoice::PAYMENT_TERM_PREPAID)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->dropColumn(['zoho_entity_id_sales_order', 'zoho_entity_id_invoice']);
        });
    }
};
