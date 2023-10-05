<?php

namespace App\Http\RequestTransformers\TermsAndCondition;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class TermsAndConditionStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return [
            'termsAndCondition' => 'terms_and_condition',
            'entity' => 'entity',
        ];
    }
}
