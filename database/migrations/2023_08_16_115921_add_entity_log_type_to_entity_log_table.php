<?php

use App\Models\EntityLog;
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
            $table->unsignedBigInteger('field_id')->nullable()->change();
            $table->string('log_type')->nullable()->default(EntityLog::EDIT_LOG_TYPE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entity_log', function (Blueprint $table) {
            $table->dropColumn('log_type');
            $table->unsignedBigInteger('field_id')->change();
        });
    }
};
