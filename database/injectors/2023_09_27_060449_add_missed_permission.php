<?php

use App\DataInjection\Injections\Injection;
use App\Models\AccountDemo;
use App\Models\AccountPartnershipStatus;
use App\Models\AccountTraining;
use App\Models\ContactAuthority;
use App\Models\ContactType;
use App\Models\CustomField;
use App\Models\Email;
use App\Models\Sequence\Sequence;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentProfile;
use App\Models\Proposal;
use App\Models\Reminder;
use App\Models\SalesTax;
use App\Models\SolutionSet;
use App\Models\SubjectLine;
use App\Models\Template;
use App\Models\TermsAndConditions;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        foreach ($this->listForUpdating() as $entity) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => $entity['entity_type'], 'type' => $entity['type']],
                $entity,
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
        //
    }

    private function listForUpdating(): array
    {
        return [
            [
                'entity_type' => ContactType::class,
                'code' => 'contact-type',
                'name' => 'Contact type',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => ContactAuthority::class,
                'code' => 'contact-authority',
                'name' => 'Contact authority',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => AccountPartnershipStatus::class,
                'code' => 'account-partnership',
                'name' => 'Account partnership',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Invoice::class,
                'code' => 'invoice',
                'name' => 'Invoice',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Template::class,
                'code' => 'pdf-template',
                'name' => 'Pdf template',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Payment::class,
                'code' => 'payments',
                'name' => 'Payments',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => PaymentProfile::class,
                'code' => 'payment-profile',
                'name' => 'Payment profile',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => SalesTax::class,
                'code' => 'sales-tax',
                'name' => 'Sales tax',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => AccountDemo::class,
                'code' => 'account-demo',
                'name' => 'Account demo',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => AccountTraining::class,
                'code' => 'account-training',
                'name' => 'Account training',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => TermsAndConditions::class,
                'code' => 'terms-and-condition',
                'name' => 'Terms and condition',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => SolutionSet::class,
                'code' => 'solution-set',
                'name' => 'Solution set',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Email::class,
                'code' => 'email-client',
                'name' => 'Email client',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => SubjectLine::class,
                'code' => 'subject-line',
                'name' => 'Subject line',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Reminder::class,
                'code' => 'reminder',
                'name' => 'Reminder',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Proposal::class,
                'code' => 'proposal',
                'name' => 'Proposal',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
            [
                'entity_type' => Sequence::class,
                'code' => 'email-sequence',
                'name' => 'Email sequence',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        ];
    }
};
