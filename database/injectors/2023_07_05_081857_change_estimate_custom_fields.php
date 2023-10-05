<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Estimate;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->updateOrCreate(
            ['entity_type' => Estimate::class, 'code' => 'opportunity'],
            [
                'entity_type' => Estimate::class,
                'code' => 'opportunity_id',
                'name' => 'Opportunity id',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'lookup_type' => null,
                'is_required' => true,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Estimate::class, 'code' => 'contact'],
            [
                'entity_type' => Estimate::class,
                'code' => 'contact_id',
                'name' => 'Contact id',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'lookup_type' => null,
                'is_required' => true,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Estimate::class, 'code' => 'account'],
            [
                'entity_type' => Estimate::class,
                'code' => 'account_id',
                'name' => 'Account id',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'lookup_type' => null,
                'is_required' => true,
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
        //
    }
};
