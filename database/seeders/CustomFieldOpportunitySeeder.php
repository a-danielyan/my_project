<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CustomFieldOpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CustomField::query()->create(
            [
                'entity_type' => Opportunity::class,
                'code' => 'opportunity-information',
                'name' => 'Opportunity Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->getOpportunityField($group1) as $group) {
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


    private function getOpportunityField($group): array
    {
        return [
            [
                'entity_type' => Opportunity::class,
                'code' => 'opportunity-owner',
                'name' => 'Opportunity owner',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'account-name',
                'name' => 'Account name',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'account',
                'sort_order' => 2,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'next-step',
                'name' => 'Next step',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'lead-source',
                'name' => 'Lead source',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_source',
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'contact-name',
                'name' => 'Contact name',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'contact',
                'sort_order' => 5,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'amount',
                'name' => 'Amount',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'probability',
                'name' => 'Probability',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 7,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'description',
                'name' => 'Description',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 8,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'opportunity_name',
                'name' => 'Opportunity name',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 9,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'project_type',
                'name' => 'Project type',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 10,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'expecting_closing_date',
                'name' => 'Expecting closing date',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 11,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'stage',
                'name' => 'Stage',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 12,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'expected_revenue',
                'name' => 'Expected revenue',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 13,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Opportunity::class,
                'code' => 'opportunity-name',
                'name' => 'Opportunity name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
        ];
    }
}
