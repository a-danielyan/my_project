<?php

namespace App\Policies;

use App\Models\TermsAndConditions;

class TermsAndConditionPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return TermsAndConditions::class;
    }
}
