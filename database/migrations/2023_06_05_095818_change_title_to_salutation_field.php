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
        Schema::table('lead', function (Blueprint $table) {
            $table->renameColumn('title', 'salutation');
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->renameColumn('title', 'salutation');
        });
        Schema::table('account', function (Blueprint $table) {
            $table->renameColumn('title', 'salutation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->renameColumn('salutation', 'title');
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->renameColumn('salutation', 'title');
        });
        Schema::table('account', function (Blueprint $table) {
            $table->renameColumn('salutation', 'title');
        });
    }
};
