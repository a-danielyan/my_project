<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Estimate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CustomFieldEstimateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CustomField::query()->create(
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate-information',
                'name' => 'Estimate Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->getProductField($group1) as $group) {
            $customField = CustomField::query()->create(Arr::except($group, 'options'));
            if ($group['type'] === CustomField::FIELD_TYPE_SELECT) {
                foreach ($group['options'] as $index => $option) {
                    CustomFieldOption::query()->create([
                        'name' => $option,
                        'custom_field_id' => $customField->getKey(),
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
    }


    private function getProductField($group): array
    {
        return [
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate-owner',
                'name' => 'Estimate owner',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'subject',
                'name' => 'Subject',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate-stage',
                'name' => 'Estimate stage',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 3,
                'parent_id' => $group->getKey(),
                'options' => [
                    'Draft',
                    'Negotiation',
                    'Delivered',
                    'On Hold',
                    'Confirmed',
                    'Closed Won',
                    'Closed Lost',
                ],
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'team',
                'name' => 'Team',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'opportunity',
                'name' => 'Opportunity',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'opportunity',
                'sort_order' => 5,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'contact',
                'name' => 'Contact',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'contact',
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'account',
                'name' => 'Account',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'account',
                'sort_order' => 7,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'billing-street',
                'name' => 'Billing street',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 8,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'billing-city',
                'name' => 'Billing city',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 9,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'billing-state',
                'name' => 'Billing state',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 10,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'billing-code',
                'name' => 'Billing code',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 11,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'billing-country',
                'name' => 'Billing country',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 12,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'adjustments',
                'name' => 'Adjustments',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 13,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'terms-and-condition',
                'name' => 'Terms and condition',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 14,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'description',
                'name' => 'Description',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 14,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate_name',
                'name' => 'Estimate name',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 15,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate_date',
                'name' => 'Estimate date',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 16,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Estimate::class,
                'code' => 'estimate_validity_duration',
                'name' => 'Estimate validity duration',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 17,
                'parent_id' => $group->getKey(),
            ],
        ];
    }
}
