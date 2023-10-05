<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\LeadStatusRepository;
use App\Http\Resource\ReferenceTables\LeadStatusResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class LeadStatusService extends BaseService
{
    /**
     * @param LeadStatusRepository $leadStatusRepository
     */
    public function __construct(
        LeadStatusRepository $leadStatusRepository,
    ) {
        $this->repository = $leadStatusRepository;
    }

    public function resource(): string
    {
        return LeadStatusResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('lead_status#' . $model->getKey());
    }
}
