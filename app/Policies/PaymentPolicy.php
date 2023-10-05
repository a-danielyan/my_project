<?php

namespace App\Policies;

use App\Models\Payment;

class PaymentPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Payment::class;
    }
}
