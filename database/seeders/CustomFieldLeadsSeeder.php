<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class CustomFieldLeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CustomField::query()->create(
            [
                'entity_type' => Lead::class,
                'code' => 'lead-information',
                'name' => 'Lead Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->getLeadsField($group1) as $group) {
            CustomField::query()->create($group);
        }
    }


    private function getLeadsField($group): array
    {
        return [
            [
                'entity_type' => Lead::class,
                'code' => 'email',
                'name' => 'Email',
                'type' => CustomField::FIELD_TYPE_EMAIL,
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'phone',
                'name' => 'Phone',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 2,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'mobile',
                'name' => 'Mobile',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 3,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'first-name',
                'name' => 'First name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'last-name',
                'name' => 'Last name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'website',
                'name' => 'Website',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'company',
                'name' => 'Company',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 7,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-owner',
                'name' => 'Lead owner',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 8,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-source',
                'name' => 'Lead source',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_source',
                'sort_order' => 9,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-status',
                'name' => 'Lead status',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_status',
                'sort_order' => 9,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-type',
                'name' => 'Lead type',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_type',
                'sort_order' => 10,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-description',
                'name' => 'Lead description',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 11,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'solution-interest',
                'name' => 'Solution interest',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'solution',
                'sort_order' => 12,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'industry',
                'name' => 'Industry',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'industries',
                'sort_order' => 13,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Lead::class,
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 14,
                'parent_id' => $group->getKey(),
            ]

        ];
    }
}
