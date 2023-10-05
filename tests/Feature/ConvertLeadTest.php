<?php

namespace Tests\Feature;

use App\Http\Services\LeadToAccountContactConvertService;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Solutions;
use Carbon\Carbon;
use Tests\TestCase;

class ConvertLeadTest extends TestCase
{
    public const LEAD_FOR_CONVERTING_ID = 21;
    public const TEST_ACCOUNT_NAME_FOR_CONVERSION = 'Test company for converting';
    public const TEST_MOBILE_FOR_CONVERSION = '123456789';
    public const TEST_DESCRIPTION_FOR_CONVERTING = 'Test description for converting';
    public const  TEST_LEAD_EMAIL_FOR_CONVERSION = 'test_lead_for_conversion@gmail.com';

    public function test_convert_lead(): void
    {
        $response = $this->post(self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount');

        $response->assertStatus(200);
        $createdEntity = json_decode($response->getContent());

        $accountId = $createdEntity->accountId;
        $contactId = $createdEntity->contactId;


        $respAccount = $this->get(self::ACCOUNT_ROUTE . '/' . $accountId);

        $resultAccount = json_decode($respAccount->getContent());


        $responseContact = $this->get(self::CONTACT_ROUTE . '/' . $contactId);

        $respAccount->assertJsonPath(
            'customFields.account-name',
            self::TEST_ACCOUNT_NAME_FOR_CONVERSION,
        ); // @todo convert to const
        $respAccount->assertJsonPath('customFields.phone', self::TEST_MOBILE_FOR_CONVERSION);
        $responseContact->assertJsonPath('customFields.phone', self::TEST_MOBILE_FOR_CONVERSION);
        $respAccount->assertJsonPath('customFields.description', self::TEST_DESCRIPTION_FOR_CONVERTING);


        $leadResponse = $this->get(self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID);
        $leadResponse->assertStatus(404);

        Lead::withTrashed()->find(self::LEAD_FOR_CONVERTING_ID)->restore();
        $leadResponse = $this->get(self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID);
        $leadResponse->assertStatus(200);
        $resultLead = json_decode($leadResponse->getContent());
        $leadResponse->assertJsonPath('customFields.lead-status.name', LeadStatus::STATUS_CONVERTED);


        $leadCreatedAt = Carbon::parse($resultLead->createdAt);

        $respAccount->assertJsonPath('customFields.lead-created-on', $leadCreatedAt->format('Y-m-d H:i:s'));
        $responseContact->assertJsonPath('customFields.lead-created-on', $leadCreatedAt->format('Y-m-d H:i:s'));

        /** @var LeadSource $leadSource */
        $leadSource = LeadSource::query()->find(1);
        $respAccount->assertJsonPath('customFields.lead-source.name', $leadSource->name);
        $responseContact->assertJsonPath('customFields.lead-source.name', $leadSource->name);
        /** @var Industry $leadIndustry */
        $leadIndustry = Industry::query()->find(1);
        $respAccount->assertJsonPath('customFields.industry.name', $leadIndustry->name);
        /** @var Solutions $solutionInterest */
        $solutionInterest = Solutions::query()->find(1);
        $respAccount->assertJsonPath('customFields.solution-interest.name', $solutionInterest->name);

        $respAccount->assertJsonPath('customFields.addresses', ['city' => 'test', 'street' => 'test']);
        $responseContact->assertJsonPath('customFields.addresses', ['city' => 'test', 'street' => 'test']);


        $this->assertTrue($resultAccount->attachments[0]->attachmentLink === '/storage/test_file');

        $this->assertDatabaseHas('activity', ['related_to_entity' => Contact::class, 'related_to_id' => $contactId]);

        $this->assertDatabaseHas(
            'email_to_entity_associations',
            ['entity' => Contact::class, 'entity_id' => $contactId],
        );

        $this->assertDatabaseHas('entity_log', ['entity' => Contact::class, 'entity_id' => $contactId]);
        $this->assertDatabaseHas('entity_log', ['entity' => Account::class, 'entity_id' => $accountId]);
    }


    public function test_convert_lead_when_contact_duplicated_no_action()
    {
        $this->createDuplicatedContact();

        $response = $this->post(self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount');
        $response->assertStatus(409);
    }


    public function test_convert_lead_when_contact_duplicated_add_to_existing()
    {
        $contact = $this->createDuplicatedContact();
        $response = $this->post(
            self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount',
            [
                'actionContact' => LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                'contactCustomFields' => $this->getContactCustomFields(),

            ],
        );
        $response->assertStatus(200);
        $createdEntity = json_decode($response->getContent());
        $contactId = $createdEntity->contactId;

        $this->assertTrue($contactId === $contact->getKey());
    }

    public function test_convert_lead_when_contact_duplicated_create_new()
    {
        $contact = $this->createDuplicatedContact();
        $response = $this->post(
            self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount',
            ['actionContact' => LeadToAccountContactConvertService::ACTION_CREATE_NEW],
        );
        $response->assertStatus(200);
        $createdEntity = json_decode($response->getContent());

        $contactId = $createdEntity->contactId;

        $this->assertTrue($contactId !== $contact->getKey());
    }


    public function test_convert_lead_when_account_duplicated_no_action()
    {
        $this->createDuplicatedAccount();

        $response = $this->post(self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount');
        $response->assertStatus(409);
    }


    public function test_convert_lead_when_account_duplicated_add_to_existing()
    {
        $account = $this->createDuplicatedAccount();
        $response = $this->post(
            self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount',
            [
                'actionAccount' => LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                'accountCustomFields' => $this->getAccountCustomFields(),
            ],
        );
        $response->assertStatus(200);
        $createdEntity = json_decode($response->getContent());
        $accountId = $createdEntity->accountId;

        $this->assertTrue($accountId === $account->getKey());
    }

    public function test_convert_lead_when_account_duplicated_create_new()
    {
        $account = $this->createDuplicatedAccount();
        $response = $this->post(
            self::LEAD_ROUTE . '/' . self::LEAD_FOR_CONVERTING_ID . '/convertToContactAccount',
            ['actionAccount' => LeadToAccountContactConvertService::ACTION_CREATE_NEW],
        );
        $response->assertStatus(200);
        $createdEntity = json_decode($response->getContent());
        $accountId = $createdEntity->accountId;

        $this->assertTrue($accountId !== $account->getKey());
    }


    private function createDuplicatedContact(): Contact
    {
        /** @var Contact $contact */
        $contact = Contact::factory()->create();
        $contactEmailCustomField = CustomField::query()->where('entity_type', Contact::class)->where(
            'code',
            'email',
        )->first();
        CustomFieldValues::query()->create([
            'field_id' => $contactEmailCustomField->getKey(),
            'entity_id' => $contact->getKey(),
            'entity' => Contact::class,
            'text_value' => self::TEST_LEAD_EMAIL_FOR_CONVERSION,
        ]);

        return $contact;
    }

    private function createDuplicatedAccount(): Account
    {
        /** @var Account $account */
        $account = Account::factory()->create();
        $accountEmailCustomField = CustomField::query()->where('entity_type', Account::class)->where(
            'code',
            'account-name',
        )->first();
        CustomFieldValues::query()->create([
            'field_id' => $accountEmailCustomField->getKey(),
            'entity_id' => $account->getKey(),
            'entity' => Account::class,
            'text_value' => self::TEST_ACCOUNT_NAME_FOR_CONVERSION,
        ]);

        return $account;
    }

    private function getContactCustomFields(): array
    {
        return [

            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
            'mobile' => fake()->phoneNumber,
        ];
    }

    private function getAccountCustomFields(): array
    {
        return [

            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
            'mobile' => fake()->phoneNumber,
        ];
    }
}
