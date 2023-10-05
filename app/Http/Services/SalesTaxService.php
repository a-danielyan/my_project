<?php

namespace App\Http\Services;

use App\Http\Repositories\SalesTaxRepository;
use App\Http\Resource\SalesTaxResource;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SalesTaxService extends BaseService
{
    public function __construct(
        SalesTaxRepository $salesTaxRepository,
    ) {
        $this->repository = $salesTaxRepository;
    }

    public function resource(): string
    {
        return SalesTaxResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }
}
