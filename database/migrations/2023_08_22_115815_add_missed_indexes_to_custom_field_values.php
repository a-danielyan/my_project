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
        Schema::table('custom_field_values', function (Blueprint $table) {
            $table->dropIndex('custom_field_values_entity_id_index');
            $table->index(['entity_id', 'entity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_field_values', function (Blueprint $table) {
            $table->dropIndex('custom_field_values_entity_id_entity_index');
        });
    }
};
