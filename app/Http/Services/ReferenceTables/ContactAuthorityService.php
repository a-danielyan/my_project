<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\ContactAuthorityRepository;
use App\Http\Resource\ReferenceTables\ContactAuthorityResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class ContactAuthorityService extends BaseService
{
    /**
     * @param ContactAuthorityRepository $contactAuthorityRepository
     */
    public function __construct(
        ContactAuthorityRepository $contactAuthorityRepository,
    ) {
        $this->repository = $contactAuthorityRepository;
    }

    public function resource(): string
    {
        return ContactAuthorityResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('lead_type#' . $model->getKey());
    }
}
