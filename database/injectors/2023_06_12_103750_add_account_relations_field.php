<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Invoice;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $customField = CustomField::query()->create(
            [
                'entity_type' => Account::class,
                'code' => 'relation',
                'name' => 'Relation',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 1,
            ],
        );

        $order = 1;
        foreach ($this->availableRelations() as $relation) {
            CustomFieldOption::query()->create([
                'name' => $relation,
                'sort_order' => $order,
                'custom_field_id' => $customField->getKey(),

            ]);
            $order++;
        }

        foreach ($this->customFieldsToCreate() as $customField) {
            CustomField::query()->create(
                $customField,
            );
        }


        $customField = CustomField::query()->create(
            [
                'entity_type' => Account::class,
                'code' => 'payment_term',
                'name' => 'Payment term',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 1,
            ],
        );

        $order = 1;
        foreach (Invoice::AVAILABLE_PAYMENT_TERMS as $relation) {
            CustomFieldOption::query()->create([
                'name' => $relation,
                'sort_order' => $order,
                'custom_field_id' => $customField->getKey(),

            ]);
            $order++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $customField = CustomField::query()->where('entity_type', Account::class)
            ->where('code', 'relation')->first();
        CustomFieldOption::query()->where('custom_field_id', $customField->getKey())->delete();
        $customField->delete();


        foreach ($this->customFieldsToCreate() as $customField) {
            CustomField::query()->where('entity_type', Account::class)->where('code', $customField['code'])->delete();
        }
    }

    private function availableRelations(): array
    {
        return [
            'End User',
            'Reseller',
            'Technology Partner',
            'Referrer',
            'Consultant',
        ];
    }

    private function customFieldsToCreate(): array
    {
        return [
            [
                'entity_type' => Account::class,
                'code' => 'website',
                'name' => 'Website',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'billing-street',
                'name' => 'Billing street',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'billing-city',
                'name' => 'Billing city',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'billing-state',
                'name' => 'Billing state',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'billing-postal-code',
                'name' => 'Billing postal code',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'billing-country',
                'name' => 'Billing country',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'sales-tax',
                'name' => 'Sales tax',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 7,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'device-count',
                'name' => 'Device count',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 8,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'support-level',
                'name' => 'Support level',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 9,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'support-level-start-date',
                'name' => 'Support level start date',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 10,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'purchased-training',
                'name' => 'Purchased Training',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 11,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'utilized-training',
                'name' => 'Utilized Training',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 12,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'demo-date',
                'name' => 'Demo date',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 13,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'demo-note',
                'name' => 'Demo note',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 14,
            ],
            [
                'entity_type' => Account::class,
                'code' => 'partnership-status',
                'name' => 'Partnership status',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'sort_order' => 15,
                'lookup_type' => 'account_partnership_status',
            ],
            [
                'entity_type' => Account::class,
                'code' => 'account-build-config',
                'name' => 'Account build config',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 16,
                'tooltip' => 'Unique build requirements for the entire account. This field will be copied to All Opportunities and Invoices for this account',
            ],


        ];
    }
};
