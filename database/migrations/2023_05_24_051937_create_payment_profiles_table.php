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
        Schema::create('payment_profile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('payment_name');
            $table->enum('payment_method', [
                Payment::PAYMENT_METHOD_CREDIT_CARD,
                Payment::PAYMENT_METHOD_ACH,
                Payment::PAYMENT_METHOD_CHECK,
                Payment::PAYMENT_METHOD_CASH,
            ]);

            $table->string('billing_street_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_profile');
    }
};
