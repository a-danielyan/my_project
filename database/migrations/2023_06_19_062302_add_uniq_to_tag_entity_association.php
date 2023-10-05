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
        Schema::table('tag_entity_association', function (Blueprint $table) {
            $table->unique(['tag_id', 'entity', 'entity_id'], 'uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tag_entity_association', function (Blueprint $table) {
            $table->dropIndex('uniq');
        });
    }
};
