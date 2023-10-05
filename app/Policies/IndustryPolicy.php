<?php

namespace App\Policies;

use App\Models\Industry;

class IndustryPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Industry::class;
    }
}
