<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\License;

return new class extends Injection
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->create(
            [
                'entity_type' => License::class,
                'code' => 'license',
                'name' => 'License',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        CustomField::query()->where('entity_type', License::class)->delete();
    }
};
