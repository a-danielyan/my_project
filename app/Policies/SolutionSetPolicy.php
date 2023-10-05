<?php

namespace App\Policies;

use App\Models\SolutionSet;

class SolutionSetPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return SolutionSet::class;
    }
}
