<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\SolutionRepository;
use App\Http\Resource\ReferenceTables\SolutionResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class SolutionService extends BaseService
{
    /**
     * @param SolutionRepository $solutionRepository
     */
    public function __construct(
        SolutionRepository $solutionRepository,
    ) {
        $this->repository = $solutionRepository;
    }

    public function resource(): string
    {
        return SolutionResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('solution#' . $model->getKey());
    }
}
