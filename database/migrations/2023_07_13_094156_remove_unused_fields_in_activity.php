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
        Schema::table('activity', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->change();
            $table->timestamp('ended_at')->nullable()->change();
            $table->string('related_to_entity')->nullable()->change();
            $table->unsignedBigInteger('related_to_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity', function (Blueprint $table) {
            //
        });
    }
};
