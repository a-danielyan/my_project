<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Role\RoleGetRequest;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;
use App\Http\RequestTransformers\Role\RoleGetSortTransformer;
use App\Http\RequestTransformers\Role\RoleTransformer;
use App\Http\Services\RoleService;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RolesController extends Controller
{
    public function __construct(private RoleService $service)
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(RoleGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new RoleGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param RoleStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        $this->service->store((new RoleTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param Role $role
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json($this->service->show($role));
    }

    /**
     * @param RoleUpdateRequest $request
     * @param Role $role
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     * @throws CustomErrorException
     */
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $this->service->update((new RoleTransformer())->transform($request), $role, $this->getUser());

        return response()->json();
    }

    /**
     * @param Role $role
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ModelDeleteErrorException
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->service->delete($role, $this->getUser());

        return response()->json();
    }
}
