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
        Schema::table('tag', function (Blueprint $table) {
            $table->dropIndex('tag_tag_unique');
            $table->string('entity_type')->after('tag')->index();
            $table->unique(['tag', 'entity_type'], 'tag_entity_uniq');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tag', function (Blueprint $table) {
            $table->dropIndex('tag_entity_uniq');
            $table->dropColumn('entity_type');
            $table->unique('tag');
        });
    }
};
