<?php

namespace App\Helpers\ZohoImport;

use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\OpportunityRepository;
use App\Models\Opportunity;
use App\Models\Stage;

class DealsZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [
            'opportunity-owner' => 'Owner',
            'account-name' => 'Account_Name',  // this is zoho id
            'amount' => 'Amount',
            'expecting_closing_date' => 'Closing_Date',
            'opportunity-name' => 'Deal_Name',
            'next-step' => 'Next_Step',
            'lead-source' => 'Lead_Source',
            'probability' => 'Probability',
            'stage' => 'Stage',
        ];
    }

    public function getEntityClassName(): string
    {
        return Opportunity::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(OpportunityRepository::class);
    }

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $cronUser = CommonHelper::getCronUser();
        $stage = Stage::query()->firstOrCreate(['name' => $this->getZohoMapperValueAsString($zohoData['Stage'])], [
            'name' => $this->getZohoMapperValueAsString($zohoData['Stage']),
            'created_by' => $cronUser->getKey(),
        ]);

        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'created_by' => $cronUser->getKey(),
            'opportunity_name' => $zohoData['Deal_Name'],
            'stage_id' => $stage->getKey(),
        ];

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }
}
