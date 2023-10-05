<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Invoice;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var CustomField $parent */
        $parent = CustomField::query()->where('entity_type', Invoice::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        foreach ($this->entityForCreate($parent) as $entity) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => $entity['entity_type'], 'code' => $entity['code']],
                $entity,
            );
        }
    }


    private function entityForCreate(CustomField $parent): array
    {
        return
            [
                [
                    'entity_type' => Invoice::class,
                    'code' => 'billing-street',
                    'name' => 'Billing street',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 8,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'billing-city',
                    'name' => 'Billing city',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 9,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'billing-state',
                    'name' => 'Billing state',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 10,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'billing-code',
                    'name' => 'Billing code',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 11,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'billing-country',
                    'name' => 'Billing country',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 12,
                    'parent_id' => $parent->getKey(),
                ],

                [
                    'entity_type' => Invoice::class,
                    'code' => 'shipping-street',
                    'name' => 'Shipping street',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 1,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'shipping-city',
                    'name' => 'Shipping city',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 2,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'shipping-state',
                    'name' => 'Shipping state',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 3,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'shipping-postal-code',
                    'name' => 'Shipping postal code',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 4,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'shipping-country',
                    'name' => 'Shipping country',
                    'type' => CustomField::FIELD_TYPE_TEXT,
                    'sort_order' => 5,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'opportunity_id',
                    'name' => 'opportunity id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 6,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'estimate_id',
                    'name' => 'estimate id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 7,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'account_id',
                    'name' => 'account id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 8,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'contact_id',
                    'name' => 'contact id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 9,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'sub_total',
                    'name' => 'sub total',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 10,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'total_tax',
                    'name' => 'total tax',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 11,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'total_discount',
                    'name' => 'total discount',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 12,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'grand_total',
                    'name' => 'grand total',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 13,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'payment_term',
                    'name' => 'payment term',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 14,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'due_date',
                    'name' => 'due date',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 15,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'terms_and_conditions',
                    'name' => 'terms and conditions',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 16,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'status',
                    'name' => 'status',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 17,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'client_po',
                    'name' => 'client po',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 18,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'parent_po',
                    'name' => 'parent po',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 19,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'previous_po',
                    'name' => 'previous po',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 20,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'notes',
                    'name' => 'notes',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 21,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'owner_id',
                    'name' => 'owner id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 22,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'order_type',
                    'name' => 'order type',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 23,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'ship_date',
                    'name' => 'ship date',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 24,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'ship_carrier',
                    'name' => 'ship carrier',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 25,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'ship_instruction',
                    'name' => 'ship instruction',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 26,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'track_code_standard',
                    'name' => 'track code standard',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 27,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'track_code_special',
                    'name' => 'track code special',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 28,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'ship_cost',
                    'name' => 'ship cost',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 29,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'cancel_reason',
                    'name' => 'cancel reason',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 30,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'cancel_details',
                    'name' => 'cancel_details',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 31,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'canceled_by',
                    'name' => 'canceled by',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 32,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'refund_amount',
                    'name' => 'refund amount',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 33,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'refund_date',
                    'name' => 'refund date',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 34,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'refund_reason',
                    'name' => 'refund reason',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 35,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'refunded_by',
                    'name' => 'refunded by',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 36,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'balance_due',
                    'name' => 'balance due',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 37,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Invoice::class,
                    'code' => 'ship-to-multiple',
                    'name' => 'Ship to multiple',
                    'type' => CustomField::FIELD_TYPE_BOOL,
                    'sort_order' => 8,
                    'parent_id' => $parent->getKey(),
                ],
            ];
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
