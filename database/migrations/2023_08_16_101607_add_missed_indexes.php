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
        Schema::table('zoho_entity_exports', function (Blueprint $table) {
            $table->index(['entity_type', 'entity_id'], 'entity_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoho_entity_exports', function (Blueprint $table) {
            $table->dropIndex('entity_type_id');
        });
    }
};
