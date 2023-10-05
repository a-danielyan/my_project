<?php

use App\DataInjection\Injections\Injection;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldOption;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->down();

        $parent = CustomField::query()->where('entity_type', Contact::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        $customField = CustomField::query()->updateOrCreate(
            [
                'entity_type' => Contact::class,
                'code' => 'department',
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'department',
                'name' => 'Department',
                'type' => CustomField::FIELD_TYPE_MULTISELECT,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        $order = 1;
        CustomFieldOption::query()->where('custom_field_id', $customField->getKey())->delete();
        foreach ($this->availableRelations() as $relation) {
            CustomFieldOption::query()->create([
                'name' => $relation,
                'sort_order' => $order,
                'custom_field_id' => $customField->getKey(),

            ]);
            $order++;
        }

        $customField = CustomField::query()->updateOrCreate(
            [
                'entity_type' => Contact::class,
                'code' => 'type',
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'type',
                'name' => 'Type',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        $order = 1;
        CustomFieldOption::query()->where('custom_field_id', $customField->getKey())->delete();
        foreach ($this->availableTypeRelations() as $relation) {
            CustomFieldOption::query()->create([
                'name' => $relation,
                'sort_order' => $order,
                'custom_field_id' => $customField->getKey(),

            ]);
            $order++;
        }

        foreach ($this->customFieldsToCreate($parent->getKey()) as $customField) {
            CustomField::query()->create(
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
        foreach ($this->customFieldsToCreate(0) as $customField) {
            CustomField::query()->where('entity_type', Contact::class)->where('code', $customField['code'])->delete();
        }
    }

    private function availableRelations(): array
    {
        return [
            'HR',
            'IT',
            'Marketing',
            'C-Suite',
            'Operations',
            'Maintenance',
        ];
    }

    private function availableTypeRelations(): array
    {
        return [
//@todo add relations
        ];
    }

    private function customFieldsToCreate($parentId): array
    {
        return [
            [
                'entity_type' => Contact::class,
                'code' => 'cms-role',
                'name' => 'CMS role',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'referencable',
                'name' => 'Referencable',
                'type' => CustomField::FIELD_TYPE_BOOL,
                'sort_order' => 2,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-street',
                'name' => 'Contact street',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-city',
                'name' => 'Contact city',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-postal-code',
                'name' => 'Contact postal code',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-state',
                'name' => 'Contact state',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-country',
                'name' => 'Contact country',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 7,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-timezone',
                'name' => 'Contact timezone',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 8,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'training-date',
                'name' => 'Training Date',
                'type' => CustomField::FIELD_TYPE_DATE,
                'sort_order' => 9,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'training-by',
                'name' => 'Trained by',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 10,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'training-notes',
                'name' => 'Training notes',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 11,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'authority',
                'name' => 'Authority',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'contact_authority',
                'sort_order' => 12,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'authority',
                'name' => 'Authority',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'contact_authority',
                'sort_order' => 13,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-type',
                'name' => 'Contact type',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'sort_order' => 14,
                'parent_id' => $parentId,
                'lookup_type'=>'contact_type'
            ],
        ];
    }
};
