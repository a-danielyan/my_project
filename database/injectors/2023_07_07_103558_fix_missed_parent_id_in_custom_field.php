<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;

return new class extends Injection {

    public const ENTITY_FOR_CHECK = [
        Account::class,
        Contact::class,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        foreach (self::ENTITY_FOR_CHECK as $entity) {
            $parent = CustomField::query()->where('entity_type', $entity)
                ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();


            CustomField::query()->where('entity_type', $entity)
                ->where('type', '!=', CustomField::FIELD_TYPE_CONTAINER)
                ->whereNull('parent_id')->update(['parent_id' => $parent->getKey()]);
        }
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
