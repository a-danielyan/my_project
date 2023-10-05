<?php

namespace App\Http\Services;

use App\Http\Repositories\TagRepository;
use App\Http\Resource\TagResource;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class TagService extends BaseService
{
    public function __construct(
        TagRepository $tagRepository,
    ) {
        $this->repository = $tagRepository;
    }

    public function resource(): string
    {
        return TagResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = 'App\Models\\' . $data['entity_type'];

        return $data;
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();
        $data['entity_type'] = 'App\Models\\' . $data['entity_type'];

        return $data;
    }
}
