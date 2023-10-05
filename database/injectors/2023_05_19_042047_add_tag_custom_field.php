<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Tag;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->create(
            [
                'entity_type' => Tag::class,
                'code' => 'tag',
                'name' => 'Tag',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
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
        CustomField::query()->where('entity_type', Tag::class)->delete();
    }
};
