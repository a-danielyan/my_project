<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Opportunity;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var CustomField $parent */
        $parent = CustomField::query()->where('entity_type', Opportunity::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        foreach ($this->entityForCreate($parent) as $entity) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => $entity['entity_type'], 'code' => $entity['code']],
                $entity,
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
        //
    }

    private function entityForCreate(CustomField $parent): array
    {
        return
            [
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'billing-street',
                    'name' => 'Billing street',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 8,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'billing-city',
                    'name' => 'Billing city',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 9,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'billing-state',
                    'name' => 'Billing state',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 10,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'billing-code',
                    'name' => 'Billing code',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 11,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'billing-country',
                    'name' => 'Billing country',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 12,
                    'parent_id' => $parent->getKey(),
                ],

                [
                    'entity_type' => Opportunity::class,
                    'code' => 'shipping-street',
                    'name' => 'Shipping street',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 1,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'shipping-city',
                    'name' => 'Shipping city',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 2,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'shipping-state',
                    'name' => 'Shipping state',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 3,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'shipping-postal-code',
                    'name' => 'Shipping postal code',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 4,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Opportunity::class,
                    'code' => 'shipping-country',
                    'name' => 'Shipping country',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 5,
                    'parent_id' => $parent->getKey(),
                ],
            ];
    }
};
