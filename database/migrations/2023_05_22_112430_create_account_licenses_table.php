<?php

use App\Models\License;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_license', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('license_id');
            $table->enum('status', [License::LICENSE_STATUS_ACTIVE, License::LICENSE_STATUS_INACTIVE])->default(
                License::LICENSE_STATUS_ACTIVE,
            );
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('license_id')->references('id')->on('license');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_license');
    }
};
