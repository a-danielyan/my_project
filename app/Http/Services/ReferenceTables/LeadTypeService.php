<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\LeadTypeRepository;
use App\Http\Resource\ReferenceTables\LeadTypeResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class LeadTypeService extends BaseService
{
    /**
     * @param LeadTypeRepository $leadTypeRepository
     */
    public function __construct(
        LeadTypeRepository $leadTypeRepository,
    ) {
        $this->repository = $leadTypeRepository;
    }

    public function resource(): string
    {
        return LeadTypeResource::class;
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
