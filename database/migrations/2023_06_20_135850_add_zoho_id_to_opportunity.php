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
        Schema::table('opportunity', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable();
        });
        Schema::table('account', function(Blueprint $table) {
            $table->renameColumn('zoho_account_number', 'zoho_entity_id');
        });
        Schema::table('contact', function(Blueprint $table) {
            $table->renameColumn('zoho_contact_number', 'zoho_entity_id');
        });
        Schema::table('estimate', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunity', function (Blueprint $table) {
            $table->dropColumn('zoho_entity_id');
        });
        Schema::table('account', function(Blueprint $table) {
            $table->renameColumn( 'zoho_entity_id','zoho_account_number');
        });
        Schema::table('contact', function(Blueprint $table) {
            $table->renameColumn( 'zoho_entity_id','zoho_contact_number');
        });
        Schema::table('estimate', function (Blueprint $table) {
            $table->dropColumn('zoho_entity_id');
        });

    }
};
