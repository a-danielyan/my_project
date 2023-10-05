<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

class CustomFieldContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CustomField::query()->create(
            [
                'entity_type' => Contact::class,
                'code' => 'contact-information',
                'name' => 'Contact Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->getContactField($group1) as $group) {
            CustomField::query()->create($group);
        }
    }


    private function getContactField($group): array
    {
        return [
            [
                'entity_type' => Contact::class,
                'code' => 'email',
                'name' => 'Email',
                'type' => CustomField::FIELD_TYPE_EMAIL,
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'phone',
                'name' => 'Phone',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 2,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'mobile',
                'name' => 'Mobile',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 3,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'first-name',
                'name' => 'First name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'last-name',
                'name' => 'Last name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-owner',
                'name' => 'Contact owner',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-source',
                'name' => 'Lead source',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_source',
                'sort_order' => 7,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'assistant',
                'name' => 'Assistant',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 8,
                'parent_id' => $group->getKey(),
            ],
            /*  [
                  'entity_type' => Contact::class,
                  'code' => 'vendor',
                  'name' => 'Vendor',
                  'type' => CustomField::FIELD_TYPE_LOOKUP,
                  'lookup_type' => 'vendor',
                  'sort_order' => 9,
                  'parent_id' => $group->getKey(),
              ],*/
            [
                'entity_type' => Contact::class,
                'code' => 'title',
                'name' => 'Title',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 10,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'department',
                'name' => 'Department',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 11,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'home-phone',
                'name' => 'Home phone',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 12,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'fax',
                'name' => 'Fax',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 13,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'date-of-birth',
                'name' => 'Date of birth',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 14,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'skype',
                'name' => 'Skype',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 15,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'twitter',
                'name' => 'Twitter',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 16,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'description',
                'name' => 'Description',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 17,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-type',
                'name' => 'Contact type',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'sort_order' => 18,
                'parent_id' => $group->getKey(),
                'lookup_type' => 'contact_type',
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-created-on',
                'name' => 'Lead Created On:',
                'type' => CustomField::FIELD_TYPE_DATETIME,
                'sort_order' => 19,
                'parent_id' => $group->getKey(),
                'deleted_at' => null,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-source',
                'name' => 'Lead Source',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_source',
                'sort_order' => 20,
                'parent_id' => $group->getKey(),
                'deleted_at' => null,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 21,
                'parent_id' => $group->getKey(),
                'deleted_at' => null,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'industry',
                'name' => 'Industry',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'industries',
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
                'deleted_at' => null,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'solution-interest',
                'name' => 'Solution interest',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'solution',
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
                'deleted_at' => null,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-title',
                'name' => 'Lead title',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-state',
                'name' => 'Contact state',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
        ];
    }
}
