<?php

namespace App\Policies;

use App\Models\Subscription;

class SubscriptionPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Subscription::class;
    }
}
