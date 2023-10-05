<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\CustomField;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->where('entity_type', Account::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        CustomField::query()->updateOrCreate(
            ['entity_type' => Account::class, 'code' => 'lead-created-on'],
            [
                'entity_type' => Account::class,
                'code' => 'lead-created-on',
                'name' => 'Lead Created On',
                'type' => CustomField::FIELD_TYPE_DATETIME,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Account::class, 'code' => 'lead-source'],
            [
                'entity_type' => Account::class,
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
            ['entity_type' => Account::class, 'code' => 'solution-interest'],
            [
                'entity_type' => Account::class,
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
            ['entity_type' => Account::class, 'code' => 'industry'],
            [
                'entity_type' => Account::class,
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
            ['entity_type' => Account::class, 'code' => 'addresses'],
            [
                'entity_type' => Account::class,
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Account::class, 'code' => 'phone'],
            [
                'entity_type' => Account::class,
                'code' => 'phone',
                'name' => 'Phone',
                'type' => CustomField::FIELD_TYPE_PHONE,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
                'deleted_at' => null,
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
