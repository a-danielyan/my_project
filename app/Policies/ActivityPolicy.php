<?php

namespace App\Policies;

use App\Models\Activity;

class ActivityPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Activity::class;
    }
}
