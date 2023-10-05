<?php

namespace App\Http\Services;

use App\Http\Repositories\PermissionRepository;
use App\Http\Resource\PermissionResource;
use App\Models\Role;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PermissionService
{
    public function __construct(private PermissionRepository $repository)
    {
    }

    public function getAllForRole(): AnonymousResourceCollection
    {
        /** @var PermissionResource $resource */
        $resource = $this->resource();

        return $resource::collection(
            $this->repository
                ->getAllShouldBeRelatedToRole(),
        );
    }

    public function update(array $params, Role $role): AnonymousResourceCollection
    {
        $role->permissions()->sync($params['permissionIds']);

        // $role->forgetCachedPermissions();  //@todo probably we need store cached permissions?

        return $this->getAllForRole();
    }


    public function resource(): string
    {
        return PermissionResource::class;
    }
}
