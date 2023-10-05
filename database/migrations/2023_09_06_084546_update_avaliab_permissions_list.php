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
        Schema::table('permission', function (Blueprint $table) {
            $actions = [
                Permission::ACTION_CREATE,
                Permission::ACTION_READ,
                Permission::ACTION_UPDATE,
                Permission::ACTION_DELETE,
                Permission::ACTION_BULK_UPDATE,
            ];

            $table->enum('action', $actions)->default(Permission::ACTION_READ)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission', function (Blueprint $table) {
            $actions = [
                Permission::ACTION_CREATE,
                Permission::ACTION_READ,
                Permission::ACTION_UPDATE,
                Permission::ACTION_DELETE,
            ];

            $table->enum('action', $actions)->default(Permission::ACTION_READ)->change();
        });
    }
};
