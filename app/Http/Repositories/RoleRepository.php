<?php

namespace App\Http\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleRepository extends BaseRepository
{
    /**
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
