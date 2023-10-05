<?php

namespace App\Http\RequestTransformers\Proposal;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ProposalStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'opportunityId' => 'opportunity_id',
                'status' => 'status',
                'templateId' => 'template_id',
                'estimates' => 'estimates',
            ];
    }
}
