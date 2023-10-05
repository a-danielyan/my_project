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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_user_profile_image_id_foreign');
            $table->dropColumn('user_profile_image_id');
            $table->string('avatar')->nullable();
        });
        Schema::dropIfExists('user_profile_image');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('user_profile_image_id')->unsigned()->nullable();
            $table->foreign('user_profile_image_id')
                ->references('id')
                ->on('user_profile_image')
                ->onDelete('set null');
            $table->dropColumn('avatar');
        });
        Schema::create('user_profile_image', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }
};
