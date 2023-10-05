<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionForRoleUpdateRequest;
use App\Http\RequestTransformers\Permission\PermissionForRolesUpdateRequestTransformer;
use App\Http\Services\PermissionService;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $service)
    {
        $this->authorizeResource(Permission::class);
    }


    public function index(Role $role): JsonResponse
    {
        return response()->json(
            $this->service
                ->getAllForRole(),
        );
    }

    public function update(Role $role, PermissionForRoleUpdateRequest $request): JsonResponse
    {
        return response()->json(
            $this->service
                ->update(
                    (new PermissionForRolesUpdateRequestTransformer())->transform($request),
                    $role,
                ),
        );
    }


    protected function resourceAbilityMap(): array
    {
        return [
            'index' => 'viewAny',
            'update' => 'updatePermission',
        ];
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return ['index', 'update'];
    }
}
