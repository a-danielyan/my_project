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
        Schema::create('subscription', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_name');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('contact_id');
            $table->string('parent_po')->nullable();
            $table->string('previous_po')->nullable();
            $table->string('order_z_number')->nullable();
            $table->timestamp('ended_at');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('invoice_id')->references('id')->on('invoice');
            $table->foreign('contact_id')->references('id')->on('contact');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription');
    }
};
