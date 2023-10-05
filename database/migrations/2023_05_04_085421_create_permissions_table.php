<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission', function (Blueprint $table) {
            $actions = [
                Permission::ACTION_CREATE,
                Permission::ACTION_READ,
                Permission::ACTION_UPDATE,
                Permission::ACTION_DELETE,
            ];
            $table->id();
            $table->unsignedBigInteger('custom_field_id');
            $table->enum('action', $actions)->default(Permission::ACTION_READ);
            $table->unique(['custom_field_id', 'action'], 'uniq_custom_field_action');
            $table->foreign('custom_field_id')->references('id')->on('custom_field');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission');
    }
};
