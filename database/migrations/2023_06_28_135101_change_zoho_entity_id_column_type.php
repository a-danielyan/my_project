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
        Schema::table('account', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable()->change();
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable()->change();
        });
        Schema::table('activity', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable();
        });
        Schema::table('opportunity', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable()->change();
        });
        Schema::table('estimate', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable()->change();
        });
        Schema::table('invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('zoho_entity_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable()->change();
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable()->change();
        });
        Schema::table('activity', function (Blueprint $table) {
            $table->dropColumn('zoho_entity_id');
        });
        Schema::table('opportunity', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable()->change();
        });
        Schema::table('estimate', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable()->change();
        });
        Schema::table('invoice', function (Blueprint $table) {
            $table->string('zoho_entity_id')->nullable()->change();
        });
    }
};
