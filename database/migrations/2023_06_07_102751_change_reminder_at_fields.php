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
        Schema::table('activity', function (Blueprint $table) {
            $table->dropColumn('reminder_type');
            $table->dropColumn('reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity', function (Blueprint $table) {
            $table->timestamp('reminder_at')->nullable();
            $table->string('reminder_type')->nullable();
        });
    }
};
