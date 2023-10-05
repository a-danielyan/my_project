<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\LeadSourceRepository;
use App\Http\Resource\ReferenceTables\LeadSourceResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class LeadSourceService extends BaseService
{
    /**
     * @param LeadSourceRepository $leadSourceRepository
     */
    public function __construct(
        LeadSourceRepository $leadSourceRepository,
    ) {
        $this->repository = $leadSourceRepository;
    }

    public function resource(): string
    {
        return LeadSourceResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('lead_source#' . $model->getKey());
    }
}
