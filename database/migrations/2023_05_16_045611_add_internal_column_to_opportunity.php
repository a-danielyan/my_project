<?php

use App\Models\Opportunity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('opportunity', function (Blueprint $table) {
            $table->string('project_name')->nullable();
            $table->enum('project_type', [
                Opportunity::EXISTED_BUSINESS,
                Opportunity::NEW_BUSINESS,
            ])->nullable();
            $table->date('expecting_closing_date')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedDecimal('expected_revenue')->nullable();
            $table->foreign('stage_id')->references('id')->on('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('opportunity', function (Blueprint $table) {
            $table->dropColumn('project_name');
            $table->dropColumn('project_type');
            $table->dropColumn('expecting_closing_date');
            $table->dropColumn('stage_id');
            $table->dropColumn('expected_revenue');
        });
        Schema::enableForeignKeyConstraints();
    }
};
