<?php

namespace App\Policies;

use App\Models\PaymentProfile;

class PaymentProfilePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return PaymentProfile::class;
    }
}
