<?php

use App\Models\Sequence\Sequence;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sequence_template_associations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sequence_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedSmallInteger('send_after');
            $table->string('send_after_unit')->default(Sequence::SEND_AFTER_UNIT_DAY);
            $table->timestamps();
            $table->foreign('sequence_id')->references('id')->on('sequence')->cascadeOnDelete();
            $table->foreign('template_id')->references('id')->on('template')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_template_associations');
    }
};
