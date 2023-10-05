<?php

namespace App\Helpers\ZohoImport;

use App\Exceptions\CustomErrorException;
use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\ContactRepository;
use App\Models\Account;
use App\Models\Contact;

class ContactsZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [
            'first-name' => 'First_Name',
            'last-name' => 'Last_Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'department' => 'Department',
            'description' => 'Description',
            'contact-type' => 'Contact_Type',
            'referencable' => 'Refrenceable',
            'contact-street' => 'Mailing_Street',
            'contact-city' => 'Mailing_City',
            'contact-country' => 'Mailing_Country',
            'contact-postal-code' => 'Mailing_Zip',
            'contact-state' => 'Mailing_State',
            'lead-source' => 'Lead_Source',
            'assistant' => 'Assistant',
            'contact-timezone' => 'Time_Zone',
            'training-date' => 'Initial_Training_Completed_Date',
            'title' => 'Title',
            'home-phone' => 'Home_Phone',
            'authority' => 'Billing_Attention',
            'date-of-birth' => 'Date_of_Birth',
            'skype' => 'Skype_ID',
            'twitter' => 'Twitter',
            'training-by' => 'Training_Completed_By',
            'training-notes' => 'Training_Follow_Up_Notes_Next_Steps',


            //@todo later go thru contacts and find where demo fields saved .  and update related Accounts

        ];
    }

    public function getEntityClassName(): string
    {
        return Contact::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(ContactRepository::class);
    }

    /**
     * @param array $zohoData
     * @param bool $isUpdate
     * @return array
     * @throws CustomErrorException
     */
    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $salutation = null;
        if (!empty($zohoData['Salutation'])) {
            $salutation = $this->getZohoMapperValueAsString($zohoData['Salutation']);
        }

        $account = Account::query()->where(
            'zoho_entity_id',
            $this->getZohoMapperValueAsString($zohoData['Account_Name']),
        )->first();

        if (!$account) {
            throw new CustomErrorException('Parent account not found');
        }

        $cronUser = CommonHelper::getCronUser();

        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'created_by' => $cronUser->getKey(),
            'salutation' => $salutation,
            'account_id' => $account->getKey(),
        ];

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }

    public function beforeMap(array $zohoData): array
    {
        if ($zohoData['Billing_Attention'] === 'true') {
            $zohoData['Billing_Attention'] = 'Billing Attention';
        }

        return $zohoData;
    }
}
