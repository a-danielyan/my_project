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
        Schema::disableForeignKeyConstraints();
        Schema::table('permission', function (Blueprint $table) {
            $table->dropForeign('permission_custom_field_id_foreign');
            $table->foreign('custom_field_id')
                ->references('id')->on('custom_field')->cascadeOnDelete();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission', function (Blueprint $table) {
            //
        });
    }
};
