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
        $this->down();

        $parent = CustomField::query()->where('entity_type', Estimate::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        foreach ($this->customFieldsToCreate($parent->getKey()) as $customField) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => Estimate::class, 'code' => $customField['code']],
                $customField,
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }


    private function customFieldsToCreate($parentId): array
    {
        return [
            [
                'entity_type' => Estimate::class,
                'code' => 'shipping-street',
                'name' => 'Shipping street',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'shipping-city',
                'name' => 'Shipping city',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'shipping-state',
                'name' => 'Shipping state',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'shipping-postal-code',
                'name' => 'Shipping postal code',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'shipping-country',
                'name' => 'Shipping country',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'notes',
                'name' => 'notes',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'phone',
                'name' => 'Phone',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 7,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'ship-to-multiple',
                'name' => 'Ship to multiple',
                'type' => CustomField::FIELD_TYPE_BOOL,
                'sort_order' => 8,
                'parent_id' => $parentId,
            ],
        ];
    }
};
