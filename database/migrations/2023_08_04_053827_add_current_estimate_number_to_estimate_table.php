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
        Schema::table('estimate', function (Blueprint $table) {
            $table->unsignedTinyInteger('estimate_number_for_opportunity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate', function (Blueprint $table) {
            $table->dropColumn('estimate_number_for_opportunity');
        });
    }
};
