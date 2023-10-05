<?php

namespace App\Http\Services\ReferenceTables;

use App\Http\Repositories\ReferenceTables\ContactTypeRepository;
use App\Http\Resource\ReferenceTables\ContactTypeResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class ContactTypeService extends BaseService
{
    /**
     * @param ContactTypeRepository $contactTypeRepository
     */
    public function __construct(
        ContactTypeRepository $contactTypeRepository,
    ) {
        $this->repository = $contactTypeRepository;
    }

    public function resource(): string
    {
        return ContactTypeResource::class;
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
