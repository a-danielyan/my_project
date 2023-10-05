<?php

namespace App\Http\Services;

use App\Http\Repositories\LicenseRepository;
use App\Http\Resource\LicenseResource;
use App\Models\License;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LicenseService extends BaseService
{
    public function __construct(
        LicenseRepository $licenseRepository,
    ) {
        $this->repository = $licenseRepository;
    }

    public function resource(): string
    {
        return LicenseResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = License::class;

        return $data;
    }


    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['entity_type'] = License::class;
        $data['updated_by'] = $user->getKey();

        return $data;
    }
}
