<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('theme_mode', [
                User::THEME_MODE_AUTO,
                User::THEME_MODE_LIGHT,
                User::THEME_MODE_DARK,
            ])->after('status')->default(User::THEME_MODE_AUTO);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme_mode');
        });
    }
};
