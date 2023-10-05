<?php

namespace App\Http\RequestTransformers\Opportunity;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class OpportunityGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return
            [
                'opportunityName' => 'opportunity_name',
                'projectType' => 'project_type',
                'stageId' => 'stage_id',
                'expectingClosingDate' => 'expecting_closing_date',
                'expectedRevenue' => 'expected_revenue',
                'accountId' => 'account_id',
            ];
    }
}
