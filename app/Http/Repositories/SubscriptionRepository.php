<?php

namespace App\Http\Repositories;

use App\Models\Subscription;

class SubscriptionRepository extends BaseRepository
{
    /**
     * @param Subscription $subscription
     */
    public function __construct(Subscription $subscription)
    {
        $this->model = $subscription;
    }
}
