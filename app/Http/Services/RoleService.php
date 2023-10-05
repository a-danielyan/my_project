<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Http\Repositories\RoleRepository;
use App\Http\Resource\RoleResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RoleService extends BaseService
{
    /**
     * @param RoleRepository $userRepository
     */
    public function __construct(
        RoleRepository $userRepository,
    ) {
        $this->repository = $userRepository;
    }

    public function resource(): string
    {
        return RoleResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        /** @var Role $model */
        if (in_array($model->name, [Role::STANDARD_USER_ROLE, Role::MAIN_ADMINISTRATOR_ROLE])) {
            throw new CustomErrorException('System defined roles cannot be edited', 422);
        }

        return $data;
    }
}
