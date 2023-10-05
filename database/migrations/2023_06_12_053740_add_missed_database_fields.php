<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->string('zoho_account_number')->nullable();
            $table->unsignedBigInteger('parent_account_id')->nullable();
            $table->foreign('parent_account_id')->references('id')->on('account');

            $table->unsignedBigInteger('lead_id')->nullable();
            $table->foreign('lead_id')->references('id')->on('lead');

        });

        Schema::table('contact', function (Blueprint $table) {
            $table->string('zoho_contact_number')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign('account_parent_account_id_foreign');
            $table->dropForeign('account_lead_id_foreign');
            $table->dropColumn('zoho_account_number');
            $table->dropColumn('parent_account_id');
            $table->dropColumn('lead_id');
        });

        Schema::table('contact', function (Blueprint $table) {

            $table->dropColumn('zoho_contact_number');
        });

    }
};
