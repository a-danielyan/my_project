<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\StageRepository;
use App\Http\Resource\ReferenceTables\StageResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StageService extends BaseService
{
    /**
     * @param StageRepository $stageRepository
     */
    public function __construct(
        StageRepository $stageRepository,
    ) {
        $this->repository = $stageRepository;
    }

    public function resource(): string
    {
        return StageResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }
}
