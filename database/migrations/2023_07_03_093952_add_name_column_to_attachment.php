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
        Schema::table('account_attachment', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
        Schema::table('contact_attachment', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
        Schema::table('subscription_attachment', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('attachment_file')->nullable();
            $table->string('attachment_link')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
        Schema::table('invoice_attachment', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_attachment', function (Blueprint $table) {
            $table->dropColumn('name')->nullable();
        });
        Schema::table('contact_attachment', function (Blueprint $table) {
            $table->dropColumn('name')->nullable();
        });
        Schema::table('subscription_attachment', function (Blueprint $table) {
            $table->dropForeign('subscription_attachment_created_by_foreign');
            $table->dropForeign('subscription_attachment_updated_by_foreign');

            $table->dropColumn('name')->nullable();
            $table->dropColumn('attachment_file');
            $table->dropColumn('attachment_link');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('invoice_attachment', function (Blueprint $table) {
            $table->dropColumn('name')->nullable();
        });
    }
};
