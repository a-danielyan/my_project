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
        Schema::create('tus_file_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('size');
            $table->string('type');
            $table->string('key')->index();
            $table->string('target')->default('source');
            $table->string('disk')->default('s3-uppy');
            $table->string('media_type');
            $table->unsignedBigInteger('media_id');
            $table->boolean('broken')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tus_file_data');
    }
};
