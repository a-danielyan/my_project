<?php

namespace App\Helpers\ZohoImport;

use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\LeadRepository;
use App\Models\Lead;

class LeadZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [
            'first-name' => 'First_Name',
            'last-name' => 'Last_Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'website' => 'Website',
            'company' => 'Company',
            'lead-description' => 'Description',
            'lead-source' => 'Lead_Source',
            'lead-status' => 'Lead_Status',
            'lead-type' => 'Lead_Type',
            'solution-interest' => 'Solution_Interest',
            'industry' => 'Industry',
        ];
    }

    public function getEntityClassName(): string
    {
        return Lead::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(LeadRepository::class);
    }
}
