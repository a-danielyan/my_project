<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\Contact;
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
        foreach ($this->listCustomFieldsForDeleting() as $customField) {
            CustomField::query()->where('entity_type', $customField['entity_type'])
                ->where('code', $customField['code'])->delete();
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

    private function listCustomFieldsForDeleting(): array
    {
        return [
            ['entity_type' => Account::class, 'code' => 'first-name'],
            ['entity_type' => Account::class, 'code' => 'last-name'],
            ['entity_type' => Account::class, 'code' => 'email'],
            ['entity_type' => Account::class, 'code' => 'phone'],
            ['entity_type' => Account::class, 'code' => 'mobile'],
            ['entity_type' => Account::class, 'code' => 'lead-source'],
            ['entity_type' => Account::class, 'code' => 'assistant'],
            ['entity_type' => Account::class, 'code' => 'title'],
            ['entity_type' => Account::class, 'code' => 'department'],
            ['entity_type' => Account::class, 'code' => 'home-phone'],
            ['entity_type' => Account::class, 'code' => 'fax'],
            ['entity_type' => Account::class, 'code' => 'date-of-birth'],
            ['entity_type' => Account::class, 'code' => 'skype'],
            ['entity_type' => Account::class, 'code' => 'twitter'],
            ['entity_type' => Contact::class, 'code' => 'contact-owner'],
            ['entity_type' => Opportunity::class, 'code' => 'contact-name'],

        ];
    }
};
