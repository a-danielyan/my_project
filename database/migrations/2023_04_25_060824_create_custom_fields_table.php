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
        Schema::create('custom_field', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->string('lookup_type')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_field');
    }
};
