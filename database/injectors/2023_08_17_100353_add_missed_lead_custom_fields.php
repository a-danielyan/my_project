<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Lead;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->where('entity_type', Lead::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'addresses'],
            [
                'entity_type' => Lead::class,
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'avatar'],
            [
                'entity_type' => Lead::class,
                'code' => 'avatar',
                'name' => 'Avatar',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'lead-title'],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-title',
                'name' => 'Lead title',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'lead-requirements'],
            [
                'entity_type' => Lead::class,
                'code' => 'lead-requirements',
                'name' => 'Lead requirements',
                'type' => CustomField::FIELD_TYPE_TEXTAREA,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'salutation'],
            [
                'entity_type' => Lead::class,
                'code' => 'salutation',
                'name' => 'Salutation',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
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
