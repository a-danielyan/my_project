<?php

namespace App\Http\Services;

use App\Http\Repositories\PaymentProfileRepository;
use App\Http\Resource\PaymentProfileResource;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PaymentProfileService extends BaseService
{
    public function __construct(
        PaymentProfileRepository $paymentProfileRepository,
    ) {
        $this->repository = $paymentProfileRepository;
    }

    public function resource(): string
    {
        return PaymentProfileResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }
}
