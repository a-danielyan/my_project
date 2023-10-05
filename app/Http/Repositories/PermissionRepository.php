<?php

namespace App\Http\Repositories;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    public function getAllShouldBeRelatedToRole(): Collection
    {
        $query = $this
            ->getQuery(
                relation: ['roleHasPermission', 'customField','roles'],
            );

        return $query->get();
    }
}
