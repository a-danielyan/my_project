<?php

namespace App\Helpers\ZohoImport;

use App\Helpers\CommonHelper;
use App\Http\Repositories\AccountRepository;
use App\Http\Repositories\BaseRepository;
use App\Models\Account;

class AccountsZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [
            'account-name' => 'Account_Name',
            'description' => 'Description',
            'account-build-config' => 'Account_Build_Config',
            'website' => 'Website',
            'billing-street' => 'Billing_Street',
            'billing-city' => 'Billing_City',
            'billing-state' => 'Billing_State',
            'billing-postal-code' => 'Billing_Code',
            'billing-country' => 'Billing_Country',
            'purchased-training' => 'Training_Hours_Purchased',
            'utilized-training' => 'Training_Hours_Utilized',

            'relation' => 'Relationship',
            'payment_term' => 'Payment_Terms',
            'partnership-status' => 'Mvix_Partner_Level',
            'sales-tax' => 'Tax_Exempt',
            'support-level' => 'Support_Level',
            'support-level-start-date' => 'Signature_Support_Start_Date',

        ];
    }

    public function getEntityClassName(): string
    {
        return Account::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(AccountRepository::class);
    }

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $cronUser = CommonHelper::getCronUser();

        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'created_by' => $cronUser->getKey(),
        ];

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }
}
