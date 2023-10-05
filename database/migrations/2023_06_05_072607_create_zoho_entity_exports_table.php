<?php

use App\Models\ZohoEntityExport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zoho_entity_exports', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->index();
            $table->unsignedBigInteger('entity_id');
            $table->json('data');
            $table->enum('sync_status', [
                ZohoEntityExport::STATUS_NEW,
                ZohoEntityExport::STATUS_IN_PROGRESS,
                ZohoEntityExport::STATUS_DONE,
                ZohoEntityExport::STATUS_ERROR,
            ]);
            $table->string('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoho_entity_exports');
    }
};
