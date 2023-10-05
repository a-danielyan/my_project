<?php

namespace App\Http\RequestTransformers\Estimate;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class EstimateStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'customFields' => 'customFields',
                'tag' => 'tag',
                'itemGroups' => 'itemGroups',
                'status' => 'status',
                'estimateName' => 'estimate_name',
                'estimateValidityDuration' => 'estimate_validity_duration',
                'opportunityId' => 'opportunity_id',
                'accountId' => 'account_id',
                'contactId' => 'contact_id',
                'discountPercent' => 'discount_percent',
            ];
    }
}
