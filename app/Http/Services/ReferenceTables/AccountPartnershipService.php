<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\AccountPartnershipRepository;
use App\Http\Resource\ReferenceTables\AccountPartnershipResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class AccountPartnershipService extends BaseService
{
    /**
     * @param AccountPartnershipRepository $accountPartnershipRepository
     */
    public function __construct(
        AccountPartnershipRepository $accountPartnershipRepository,
    ) {
        $this->repository = $accountPartnershipRepository;
    }

    public function resource(): string
    {
        return AccountPartnershipResource::class;
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
