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
        Schema::table('entity_log', function (Blueprint $table) {
            $table->text('previous_value')->change();
            $table->text('new_value')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entity_log', function (Blueprint $table) {
            $table->string('previous_value')->change();
            $table->string('new_value')->change();
        });
    }
};
