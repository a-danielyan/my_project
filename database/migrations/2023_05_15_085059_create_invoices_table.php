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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opportunity_id');
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedDecimal('sub_total');
            $table->unsignedDecimal('total_tax');
            $table->unsignedDecimal('total_discount');
            $table->unsignedDecimal('grand_total');
            $table->enum('payment_term', Invoice::AVAILABLE_PAYMENT_TERMS)->default(Invoice::PAYMENT_TERM_PREPAID);
            $table->date('due_date')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->enum('status', Invoice::AVAILABLE_INVOICE_STATUSES)->default(Invoice::INVOICE_STATUS_DRAFT);
            $table->string('filename')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('opportunity_id')->references('id')->on('opportunity');
            $table->foreign('estimate_id')->references('id')->on('estimate');
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('contact_id')->references('id')->on('contact');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
