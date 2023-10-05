<?php

use App\Models\Estimate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estimate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('estimate_name')->nullable();
            $table->date('estimate_date')->nullable();
            $table->date('estimate_validity_duration')->nullable();
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->enum('status', Estimate::AVAILABLE_STATUSES);

            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('opportunity_id')->references('id')->on('opportunity');
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('contact_id')->references('id')->on('contact');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate');
    }
};
