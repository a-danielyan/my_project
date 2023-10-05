<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('payment_name');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedDecimal('payment_received');
            $table->enum('payment_method', [
                Payment::PAYMENT_METHOD_CREDIT_CARD,
                Payment::PAYMENT_METHOD_ACH,
                Payment::PAYMENT_METHOD_CHECK,
                Payment::PAYMENT_METHOD_CASH,
            ]);
            $table->timestamp('payment_date');
            $table->unsignedDecimal('balance_due');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('received_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('refund_invoice')->nullable();

            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('invoice_id')->references('id')->on('invoice');
            $table->foreign('received_by')->references('id')->on('users');
            $table->foreign('refund_invoice')->references('id')->on('invoice');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
