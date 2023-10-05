<?php

use App\Models\Payment;
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
            $table->string('payment_source')->nullable()->after('payment_method');
            $table->string('credit_card_type')->nullable();
            $table->string('payment_processor')->nullable();
            $table->enum('payment_method', [
                Payment::AVAILABLE_PAYMENT_METHODS,
            ])->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->dropColumn('payment_source', 'credit_card_type', 'payment_processor');
        });
    }
};
