<?php

use App\DataInjection\Injections\Injection;
use App\Helpers\CommonHelper;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $cronUser = CommonHelper::getCronUser();
        $account = CommonHelper::getOrCreateDefaultAccount();

        $contact = Contact::query()->create([
            'created_by' => $cronUser->getKey(),
            'account_id' => $account->getKey(),
        ]);

        $customFieldContactFirstName = CustomField::query()->where('code', 'first-name')
            ->where('entity_type', Contact::class)->first();
        $customFieldContactLastName = CustomField::query()->where('code', 'last-name')
            ->where('entity_type', Contact::class)->first();

        if ($customFieldContactFirstName) {
            CustomFieldValues::query()->create([
                'field_id' => $customFieldContactFirstName->getKey(),
                'entity_id' => $contact->getKey(),
                'entity' => Contact::class,
                'text_value' => Contact::DEFAULT_CONTACT_FIRST_NAME,
            ]);
        }

        if ($customFieldContactLastName) {
            CustomFieldValues::query()->create([
                'field_id' => $customFieldContactLastName->getKey(),
                'entity_id' => $contact->getKey(),
                'entity' => Contact::class,
                'text_value' => Contact::DEFAULT_CONTACT_LAST_NAME,
            ]);
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
