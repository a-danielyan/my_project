<?php

namespace App\Helpers\ZohoImport;

use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\EstimateRepository;
use App\Models\Estimate;

class QuoteZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [
            'terms-and-condition' => 'Terms_and_Conditions',
            'shipping-street' => 'Shipping_Street',
            'shipping-city' => 'Shipping_City',
            'shipping-state' => 'Shipping_State',
            'shipping-postal-code' => 'Shipping_Code',
            'shipping-country' => 'Shipping_Country',
            'description' => 'Description',
            'adjustments' => 'Adjustment',
            'billing-country' => 'Billing_Country',
            'billing-code' => 'Billing_Code',
            'billing-state' => 'Billing_State',
            'billing-city' => 'Billing_City',
            'billing-street' => 'Billing_Street',
            'team' => 'Team',
        ];
    }

    public function getEntityClassName(): string
    {
        return Estimate::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(EstimateRepository::class);
    }

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $cronUser = CommonHelper::getCronUser();
        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'created_by' => $cronUser->getKey(),
            'estimate_number' => $zohoData['Estimate_No'],
            'estimate_validity_duration' => !empty($zohoData['Valid_Till']) ? $this->getZohoMapperValueAsString(
                $zohoData['Valid_Till'],
            ) : null,
            'estimate_date' => $zohoData['Created_Time'],
            'estimate_name' => $zohoData['Subject'],
            //   'opportunity_id'=> $zohoData['Deal_Name'], //synced later
            //   'account_id'=> $zohoData['Account_Name'], //synced later
            //   'contact_id'=> $zohoData['Contact_Name'], //synced later
            'status' => $this->getZohoMapperValueAsString($zohoData['Quote_Stage']),
        ];

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }
}
