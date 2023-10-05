<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Opportunity;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->where('entity_type', Opportunity::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        CustomField::query()->updateOrCreate(
            ['entity_type' => Opportunity::class, 'code' => 'contact-name'],
            [
                'entity_type' => Opportunity::class,
                'code' => 'contact-name',
                'name' => 'Contact name',
                'type' => CustomField::FIELD_TYPE_LOOKUP,
                'lookup_type' => 'contact',
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Opportunity::class, 'code' => 'account-name'],
            [
                'entity_type' => Opportunity::class,
                'code' => 'account-name',
                'name' => 'Account name',
                'type' => CustomField::FIELD_TYPE_INTERNAL,
                'lookup_type' => null,
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
