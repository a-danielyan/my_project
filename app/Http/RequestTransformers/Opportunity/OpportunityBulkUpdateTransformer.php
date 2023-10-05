<?php

namespace App\Http\RequestTransformers\Opportunity;

use App\Http\RequestTransformers\BaseBulkUpdateTransformer;

class OpportunityBulkUpdateTransformer extends BaseBulkUpdateTransformer
{
    protected function getMap(): array
    {
        return array_merge(
            parent::getMap(),
            [
                "projectName" => 'project_name',
                "stageId" => 'stage_id',
                "projectType" => "project_type",
                'expectingClosingDate' => 'expecting_closing_date',
            ],
        );
    }
}
