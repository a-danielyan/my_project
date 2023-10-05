<?php

namespace App\Http\Services;

use App\Http\Repositories\SubscriptionRepository;
use App\Http\Resource\SubscriptionResource;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SubscriptionService extends BaseService
{
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
    ) {
        $this->repository = $subscriptionRepository;
    }

    public function resource(): string
    {
        return SubscriptionResource::class;
    }

    public function getAll(array $params, Authenticatable $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }
}
