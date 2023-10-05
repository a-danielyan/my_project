<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountAttachment;
use App\Models\AccountDemo;
use App\Models\AccountTraining;
use App\Models\Activity;
use App\Models\ActivityReminder;
use App\Models\Contact;
use App\Models\ContactAttachments;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Email;
use App\Models\EmailToEntityAssociation;
use App\Models\EntityLog;
use App\Models\EstimateAttachment;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\Lead;
use App\Models\LeadAttachments;
use App\Models\License;
use App\Models\OauthToken;
use App\Models\Opportunity;
use App\Models\OpportunityAttachment;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\Sequence\Sequence;
use App\Models\SubjectLine;
use App\Models\Template;
use App\Models\Preference;
use App\Models\Product;
use App\Models\Estimate;
use App\Models\ProductAttachment;
use App\Models\SalesTax;
use App\Models\SolutionSet;
use App\Models\SolutionSetItems;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\TermsAndConditions;
use App\Models\User;
use Tests\Feature\ConvertLeadTest;
use Illuminate\Database\Seeder;
use Tests\TestCase;

class TestDatabaseSeeder extends Seeder
{
    public const TEST_ADMIN_USER_EMAIL = 'tester@gmail.com';
    public const TEST_CONSULTANT_USER_EMAIL = 'tester_consultant@gmail.com';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createTestUser();
        $this->createTestLead();
        $this->createTestAccount();
        $this->createTestContact();
        $this->createTestOpportunity();
        $this->createTestProduct();
        $this->createTestEstimate();
        $this->createTestActivity();
        $this->createTestTag();
        $this->createTestSubscription();
        $this->createTestLicenses();
        $this->createTestPayments();
        $this->createTestInvoices();
        $this->createTestAccountAttachments();
        $this->createTestContactAttachments();
        $this->createTestLeadAttachments();
        $this->createTestInvoiceAttachments();
        $this->createTestOpportunityAttachments();
        $this->createTestProductAttachments();
        $this->createTestEstimateAttachments();
        $this->createTestZohoToken();
        $this->createTestTemplates();
        $this->createTestSalesTax();
        $this->createTestPreferences();
        $this->createTestAccountDemo();
        $this->createTestAccountTraining();
        $this->createTestLeadForConvertingToAccount();
        $this->createTestTermsAndConditions();
        $this->createTestSolutionSets();
        $this->createTestEmailToken();
        $this->createTestEmails();
        $this->createTestSubjectLine();
        $this->createTestReminder();
        $this->createTestSequence();
    }

    private function createTestUser(): void
    {
        User::query()->create(
            [
                'first_name' => 'Test admin',
                'last_name' => 'Tester',
                'role_id' => 1,
                'email' => self::TEST_ADMIN_USER_EMAIL,
            ],
        );
        User::query()->create(
            [
                'first_name' => 'Test consultant',
                'last_name' => 'Tester',
                'role_id' => 2,
                'email' => self::TEST_CONSULTANT_USER_EMAIL,
            ],
        );
        User::query()->create(
            [
                'first_name' => 'AJ',
                'last_name' => 'Jay',
                'role_id' => 1,
                'email' => User::AJAY_EMAIL,
            ],
        );
        User::query()->create(
            [
                'first_name' => 'Test',
                'last_name' => 'Cron',
                'role_id' => 1,
                'email' => User::EMAIL_FOR_CRON_USER,
            ],
        );
    }

    private function createTestLead(): void
    {
        Lead::factory()->count(10)->create();
        $item = new Lead();
        $item->id = 20;
        $item->created_by = 1;
        $item->deleted_at = now();
        $item->save();
    }

    private function createTestLeadForConvertingToAccount(): void
    {
        $item = new Lead();
        $item->id = ConvertLeadTest::LEAD_FOR_CONVERTING_ID;
        $item->created_by = 1;
        $item->save();

        $leadCustomFields = CustomField::query()->where('entity_type', Lead::class)->get()->mapWithKeys(
            function ($item) {
                return [$item->code => $item];
            },
        );

        $customFieldsToCreate = [
            'email' => ['text_value' => ConvertLeadTest::TEST_LEAD_EMAIL_FOR_CONVERSION],
            'company' => ['text_value' => ConvertLeadTest::TEST_ACCOUNT_NAME_FOR_CONVERSION],
            'phone' => ['text_value' => ConvertLeadTest::TEST_MOBILE_FOR_CONVERSION],
            'lead-description' => ['text_value' => ConvertLeadTest::TEST_DESCRIPTION_FOR_CONVERTING],
            'lead-status' => ['integer_value' => 1],
            'lead-source' => ['integer_value' => 1],
            'solution-interest' => ['integer_value' => 1],
            'industry' => ['integer_value' => 1],
            'addresses' => ['json_value' => ['street' => 'test', 'city' => 'test']],
        ];

        foreach ($customFieldsToCreate as $code => $value) {
            $this->createCustomFieldValues($leadCustomFields[$code]->getKey(), $value);
        }

        LeadAttachments::query()->create([
            'lead_id' => ConvertLeadTest::LEAD_FOR_CONVERTING_ID,
            'attachment_link' => '/storage/test_file',
            'created_by' => 1,
            'name' => 'test attachment name',
        ]);

        Activity::query()->create([
            'related_to' => 1,
            'activity_type' => 'Task',
            'activity_status' => 'Not started',
            'subject' => 'Test subject',
            'created_by' => 1,
            'related_to_entity' => Lead::class,
            'related_to_id' => ConvertLeadTest::LEAD_FOR_CONVERTING_ID,

        ]);


        $token = OauthToken::query()->create([
            'user_id' => 1,
            'access_token' => 'token',
            'expire_on' => now(),
        ]);


        $email = Email::query()->create([
            'token_id' => $token->getKey(),
            'email_id' => 'test_email_id',
            'received_date' => now(),
            'subject' => 'Test email subject',
        ]);

        EmailToEntityAssociation::query()->create(
            [
                'email_id' => $email->getKey(),
                'entity_id' => ConvertLeadTest::LEAD_FOR_CONVERTING_ID,
                'entity' => Lead::class,
            ],
        );

        EntityLog::query()->create([
            'entity' => Lead::class,
            'entity_id' => ConvertLeadTest::LEAD_FOR_CONVERTING_ID,
            'field_id' => $leadCustomFields['email']->id,
            'previous_value' => 'old value',
            'new_value' => 'new value',
            'updated_by' => 1,
            'update_id' => 1,
        ]);
    }


    private function createCustomFieldValues(int $fieldId, array $value): void
    {
        CustomFieldValues::query()->create(
            array_merge(
                [
                    'field_id' => $fieldId,
                    'entity_id' => ConvertLeadTest::LEAD_FOR_CONVERTING_ID,
                    'entity' => Lead::class,
                ],
                $value,
            ),
        );
    }

    private function createTestOpportunity(): void
    {
        Opportunity::factory()->count(10)->create();
    }

    private function createTestProduct(): void
    {
        Product::factory()->count(10)->create();
        // Product for delete
        $item = new Product();
        $item->id = 20;
        $item->created_by = 1;
        $item->deleted_at = now();
        $item->save();


        $this->createProductWithSpecificPriceAndId(10, TestCase::PRODUCT_ID_WITH_PRICE_10);
        $this->createProductWithSpecificPriceAndId(20, TestCase::PRODUCT_ID_WITH_PRICE_20);
        $this->createProductWithSpecificPriceAndId(100, TestCase::PRODUCT_ID_WITH_PRICE_100);
    }


    private function createProductWithSpecificPriceAndId(float $price, int $productId): void
    {
        Product::query()->insert([
            'id' => $productId,
            'created_by' => 1,
        ]);
        $priceCustomField = CustomField::query()->where('entity_type', Product::class)
            ->where('code', 'product-price')->first();


        CustomFieldValues::query()->create([
            'field_id' => $priceCustomField->getKey(),
            'entity' => Product::class,
            'entity_id' => $productId,
            'float_value' => $price,
        ]);
    }


    private function createTestAccount(): void
    {
        Account::factory()->count(10)->create();
        $item = new Account();
        $item->id = 20;
        $item->created_by = 1;
        $item->deleted_at = now();
        $item->save();
    }

    private function createTestContact(): void
    {
        Contact::factory()->count(10)->create();
        $item = new Contact();
        $item->id = 20;
        $item->account_id = 1;
        $item->created_by = 1;
        $item->deleted_at = now();
        $item->save();


        Contact::query()->insert([
            'id' => TestCase::CONTACT_WITH_TAXES_ID,
            'account_id' => 1,
            'created_by' => 1,
        ]);


        $stateCustomField = CustomField::query()->where('entity_type', Contact::class)
            ->where('code', 'contact-state')->first();


        CustomFieldValues::query()->create([
            'field_id' => $stateCustomField->getKey(),
            'entity' => Product::class,
            'entity_id' => TestCase::CONTACT_WITH_TAXES_ID,
            'text_value' => TestCase::STATE_WITH_TAXES,
        ]);
    }

    private function createTestEstimate(): void
    {
        Estimate::factory()->count(10)->create();
    }

    private function createTestActivity(): void
    {
        Activity::factory()->count(10)->create();

        ActivityReminder::query()->create([
            'activity_id' => 1,
            'reminder_type' => 'email',
            'reminder_time' => 20,
            'reminder_unit' => 'minutes',

        ]);
    }

    private function createTestTag(): void
    {
        Tag::factory()->count(10)->create();
    }

    private function createTestSubscription(): void
    {
        Subscription::factory()->count(10)->create();
    }

    private function createTestLicenses(): void
    {
        License::factory()->count(10)->create();
    }

    private function createTestPayments(): void
    {
        Payment::factory()->count(10)->create();
    }

    private function createTestInvoices(): void
    {
        Invoice::factory()->count(10)->create();
    }

    private function createTestAccountAttachments(): void
    {
        AccountAttachment::query()->create([
            'account_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        AccountAttachment::query()->create([
            'account_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestContactAttachments(): void
    {
        ContactAttachments::query()->create([
            'contact_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        ContactAttachments::query()->create([
            'contact_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestInvoiceAttachments(): void
    {
        InvoiceAttachment::query()->create([
            'invoice_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        InvoiceAttachment::query()->create([
            'invoice_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestOpportunityAttachments(): void
    {
        OpportunityAttachment::query()->create([
            'opportunity_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        OpportunityAttachment::query()->create([
            'opportunity_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestProductAttachments(): void
    {
        ProductAttachment::query()->create([
            'product_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        ProductAttachment::query()->create([
            'product_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestEstimateAttachments(): void
    {
        EstimateAttachment::query()->create([
            'estimate_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        EstimateAttachment::query()->create([
            'estimate_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }


    private function createTestLeadAttachments(): void
    {
        LeadAttachments::query()->create([
            'lead_id' => 1,
            'attachment_link' => 'https://test.com',
            'created_by' => '1',
        ]);
        LeadAttachments::query()->create([
            'lead_id' => 1,
            'attachment_link' => 'https://test1.com',
            'created_by' => '1',
        ]);
    }

    private function createTestZohoToken(): void
    {
        OauthToken::query()->create([
            'user_id' => 1,
            'access_token' => 'token',
            'grant_token' => 'token',
            'refresh_token' => 'token',
            'expire_on' => now(),

        ]);
    }

    private function createTestEmailToken(): void
    {
        OauthToken::query()->create([
            'user_id' => 1,
            'access_token' => 'token',
            'grant_token' => 'token',
            'refresh_token' => 'token',
            'expire_on' => now(),
            'service' => 'Gmail',

        ]);
    }


    private function createTestTemplates(): void
    {
        Template::query()->create([
            'entity' => Template::TEMPLATE_TYPE_EMAIL,
            'created_by' => 1,
            'template' => fake()->randomHtml,
        ]);
        Template::query()->create([
            'entity' => Template::TEMPLATE_TYPE_PROPOSAL,
            'created_by' => 1,
            'template' => fake()->randomHtml,
            'is_default' => 1,
        ]);
    }

    private function createTestSalesTax(): void
    {
        SalesTax::query()->create([
            'state_code' => 'AZ',
            'created_by' => 1,
            'tax' => fake()->randomFloat(2, 0, 99),
        ]);

        SalesTax::query()->create([
            'state_code' => 'FL',
            'created_by' => 1,
            'tax' => fake()->randomFloat(2, 0, 99),
        ]);
        SalesTax::query()->create([
            'state_code' => 'GA',
            'created_by' => 1,
            'tax' => fake()->randomFloat(2, 0, 99),
        ]);

        SalesTax::query()->create([
            'state_code' => 'IL',
            'created_by' => 1,
            'tax' => fake()->randomFloat(2, 0, 99),
        ]);

        SalesTax::query()->create([
            'state_code' => TestCase::STATE_WITH_TAXES,
            'created_by' => 1,
            'tax' => TestCase::STATE_TAXES_PERCENT,
        ]);
    }

    private function createTestPreferences(): void
    {
        Preference::query()->create([
            'user_id' => 1,
            'entity' => 'Account',
            'name' => fake()->text(25),
            'settings' => [
                'filter' => [
                    ['key' => 'val'],
                ],
            ],
        ]);
    }

    private function createTestAccountDemo(): void
    {
        AccountDemo::query()->create([
            'account_id' => 1,
            'demo_date' => fake()->date(),
            'trained_by' => 1,
            'created_by' => 1,
            'note' => fake()->text,
        ]);
    }

    private function createTestAccountTraining(): void
    {
        AccountTraining::query()->create([
            'account_id' => 1,
            'training_date' => fake()->date(),
            'trained_by' => 1,
            'created_by' => 1,
            'note' => fake()->text,
        ]);
    }

    public function createTestTermsAndConditions(): void
    {
        TermsAndConditions::query()->create([
            'entity' => TermsAndConditions::ESTIMATE_ENTITY,
            'terms_and_condition' => fake()->randomHtml,
            'created_by' => 1,
        ]);
        TermsAndConditions::query()->create([
            'entity' => TermsAndConditions::INVOICE_ENTITY,
            'terms_and_condition' => fake()->randomHtml,
            'created_by' => 1,
        ]);
        TermsAndConditions::query()->create([
            'entity' => TermsAndConditions::PROPOSAL_ENTITY,
            'terms_and_condition' => fake()->randomHtml,
            'created_by' => 1,
        ]);
    }

    private function createTestSolutionSets(): void
    {
        $solutionSet1 = SolutionSet::query()->create([
            'name' => fake()->name,
            'created_by' => 1,
        ]);
        $solutionSet2 = SolutionSet::query()->create([
            'name' => fake()->name,
            'created_by' => 1,
        ]);

        SolutionSetItems::factory(['solution_set_id' => $solutionSet1->getKey()])->count(5)->create();
        SolutionSetItems::factory(['solution_set_id' => $solutionSet2->getKey()])->count(5)->create();
    }

    private function createTestEmails(): void
    {
        Email::query()->create([
            'token_id' => 2,
            'email_id' => 'test_email',
            'received_date' => now(),
            'from' => fake()->email,
            'to' => [fake()->email],
            'subject' => 'test subject',
        ]);
    }

    private function createTestSubjectLine(): void
    {
        SubjectLine::query()->create([
            'subject_text' => fake()->text(50),
            'created_by' => 1,
        ]);
    }

    private function createTestReminder(): void
    {
        Reminder::query()->create([
            'name' => fake()->text(20),
            'related_entity' => "Subscription",
            'remind_entity' => "Account",
            'remind_days' => 2,
            'remind_type' => 'before',
            'sender' => [fake()->email],
            'subject' => fake()->text,
            'reminder_text' => fake()->randomHtml,
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
        ]);
    }

    private function createTestSequence(): void
    {
        Sequence::query()->create([
            'name' => fake()->text(20),
            'start_date' => fake()->date(),
            'is_active' => 1,
            'created_by' => 1,
        ]);
    }
}
