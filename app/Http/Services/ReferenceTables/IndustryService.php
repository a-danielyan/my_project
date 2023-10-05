<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\IndustryRepository;
use App\Http\Resource\ReferenceTables\IndustryResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class IndustryService extends BaseService
{
    /**
     * @param IndustryRepository $industryRepository
     */
    public function __construct(
        IndustryRepository $industryRepository,
    ) {
        $this->repository = $industryRepository;
    }

    public function resource(): string
    {
        return IndustryResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }


    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('industries#' . $model->getKey());
    }
}
