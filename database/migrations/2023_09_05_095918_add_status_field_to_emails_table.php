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
        Schema::table('email', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->json('schedule_details')->nullable();
            $table->string('error')->nullable();
            $table->string('email_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email', function (Blueprint $table) {
            $table->dropColumn('status', 'send_at', 'schedule_details', 'error');
        });
    }
};
