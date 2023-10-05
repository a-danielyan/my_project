<?php

namespace App\Policies;

use App\Models\Solutions;

class SolutionPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Solutions::class;
    }
}
