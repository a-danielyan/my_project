<?php

namespace App\Http\RequestTransformers\Opportunity;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class OpportunityStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'customFields' => 'customFields',
                'tag' => 'tag',
                'status' => 'status',
                'opportunityName' => 'opportunity_name',
                'projectType' => 'project_type',
                'stageId' => 'stage_id',
                'expectingClosingDate' => 'expecting_closing_date',
                'expectedRevenue' => 'expected_revenue',
                'accountId' => 'account_id',
                'internalNotes' => 'internalNotes',
            ];
    }
}
