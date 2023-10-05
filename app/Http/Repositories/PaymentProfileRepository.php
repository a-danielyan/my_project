<?php

namespace App\Http\Repositories;

use App\Models\PaymentProfile;

class PaymentProfileRepository extends BaseRepository
{
    /**
     * @param PaymentProfile $paymentProfile
     */
    public function __construct(
        PaymentProfile $paymentProfile,
    ) {
        $this->model = $paymentProfile;
    }
}
