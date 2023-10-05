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
        Schema::table('custom_field', function (Blueprint $table) {
            $table->string('tooltip')->nullable();
            $table->enum('tooltip_type', ['icon', 'text'])->default('icon');
            $table->json('property')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_field', function (Blueprint $table) {
            $table->dropColumn('tooltip');
            $table->dropColumn('tooltip_type');
            $table->dropColumn('property');
        });
    }
};
