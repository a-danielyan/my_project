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
            $table->timestamp('apollo_synced_at')->nullable();
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->timestamp('apollo_synced_at')->nullable();
        });
        Schema::table('lead', function (Blueprint $table) {
            $table->timestamp('apollo_synced_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('apollo_synced_at');
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('apollo_synced_at');
        });
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('apollo_synced_at');
        });
    }
};
