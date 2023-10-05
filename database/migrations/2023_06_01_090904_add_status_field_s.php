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
        Schema::table('account', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });

        Schema::table('activity', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('device', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('lead', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('license', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('opportunity', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('product', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('role', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });
        Schema::table('tag', function (Blueprint $table) {
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default('Active');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('activity', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('device', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('license', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('opportunity', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('role', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('tag', function (Blueprint $table) {
            $table->dropColumn('status');
        });

    }
};
