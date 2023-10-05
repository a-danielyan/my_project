<?php

use App\DataInjection\Injections\Injection;
use App\Models\Contact;
use App\Models\CustomField;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->where('entity_type', Contact::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'type' => CustomField::FIELD_TYPE_DATETIME],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-created-on',
                'name' => 'Lead Created On:',
                'type' => CustomField::FIELD_TYPE_DATETIME,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'lead-source'],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-source',
                'name' => 'Lead Source',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'lead_source',
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'addresses'],
            [
                'entity_type' => Contact::class,
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'contact-owner'],
            [
                'entity_type' => Contact::class,
                'code' => 'contact-owner',
                'name' => 'Contact owner',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'users',
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'industry'],
            [
                'entity_type' => Contact::class,
                'code' => 'industry',
                'name' => 'Industry',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'industries',
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'solution-interest'],
            [
                'entity_type' => Contact::class,
                'code' => 'solution-interest',
                'name' => 'Solution interest',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'solution',
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'lead-title'],
            [
                'entity_type' => Contact::class,
                'code' => 'lead-title',
                'name' => 'Lead title',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );
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
