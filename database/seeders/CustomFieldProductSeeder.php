<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CustomFieldProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CustomField::query()->create(
            [
                'entity_type' => Product::class,
                'code' => 'product-information',
                'name' => 'Product Information',
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
                'entity_type' => Product::class,
                'code' => 'product-image',
                'name' => 'Product image',
                'type' => CustomField::FIELD_TYPE_IMAGE,
                'sort_order' => 1,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-code',
                'name' => 'Product code',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-active',
                'name' => 'Product active',
                'type' => CustomField::FIELD_TYPE_CHECKBOX,
                'sort_order' => 3,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-category',
                'name' => 'Product category',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
                'options' => [
                    'Hardware',
                    'Software',
                ],
            ],
            [
                'entity_type' => Product::class,
                'code' => 'sales-end-date',
                'name' => 'Sales end date',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 5,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'support-end-date',
                'name' => 'Support end date',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 6,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-name',
                'name' => 'Product name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 7,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-description',
                'name' => 'Product description',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 8,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-price',
                'name' => 'Product price',
                'type' => CustomField::FIELD_TYPE_NUMBER,
                'sort_order' => 9,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-recurring',
                'name' => 'Product recurring',
                'type' => CustomField::FIELD_TYPE_CHECKBOX,
                'sort_order' => 10,
                'parent_id' => $group->getKey(),
            ],
            [
                'entity_type' => Product::class,
                'code' => 'Recurring-frequency',
                'name' => 'Recurring frequency',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 4,
                'parent_id' => $group->getKey(),
                'options' => [
                    'Monthly',
                    'Quarterly',
                    'Yearly',
                    '2 Year',
                ],
            ],


        ];
    }
}
