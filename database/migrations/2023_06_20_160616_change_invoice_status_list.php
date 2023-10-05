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
        Schema::disableForeignKeyConstraints();
        Schema::table('invoice', function (Blueprint $table) {
            $table->enum('status', Invoice::AVAILABLE_INVOICE_STATUSES)->change();
            $table->string('invoice_number')->nullable();
            $table->string('client_po')->nullable();
            $table->string('parent_po')->nullable();
            $table->string('previous_po')->nullable();
            $table->string('zoho_entity_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('order_type')->nullable();
            $table->date('ship_date')->nullable();
            $table->string('ship_carrier')->nullable();
            $table->text('ship_instruction')->nullable();
            $table->string('track_code_standard')->nullable();
            $table->string('track_code_special')->nullable();
            $table->string('ship_cost')->nullable();
            $table->unsignedInteger('sql_to_order_duration')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->text('cancel_details')->nullable();
            $table->unsignedBigInteger('canceled_by')->nullable();
            $table->unsignedFloat('refund_amount')->nullable();
            $table->date('refund_date')->nullable();
            $table->string('refund_reason')->nullable();
            $table->unsignedBigInteger('refunded_by')->nullable();


            $table->foreign('payment_id')->references('id')->on('payment');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('canceled_by')->references('id')->on('users');
            $table->foreign('refunded_by')->references('id')->on('users');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('invoice', function (Blueprint $table) {
            $table->dropForeign('invoice_payment_id_foreign');
            $table->dropForeign('invoice_owner_id_foreign');
            $table->dropForeign('invoice_canceled_by_foreign');
            $table->dropForeign('invoice_refunded_by_foreign');

            $table->dropColumn([
                'invoice_number',
                'client_po',
                'parent_po',
                'previous_po',
                'zoho_entity_id',
                'notes',
                'payment_id',
                'owner_id',
                'order_type',
                'ship_date',
                'ship_carrier',
                'ship_instruction',
                'track_code_standard',
                'track_code_special',
                'ship_cost',
                'sql_to_order_duration',
                'cancel_reason',
                'cancel_details',
                'canceled_by',
                'refund_amount',
                'refund_date',
                'refund_reason',
                'refunded_by',
            ]);
        });
        Schema::enableForeignKeyConstraints();
    }
};
