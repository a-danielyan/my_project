<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\User\UserGetRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\RequestTransformers\User\UserGetSortTransformer;
use App\Http\RequestTransformers\User\UserTransformer;
use App\Http\Services\User\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $service)
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * @param UserGetRequest $request
     * @return JsonResponse
     */
    public function index(UserGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new UserGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param UserStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        return response()->json($this->service->store((new UserTransformer())->transform($request), $this->getUser()));
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($this->service->show($user));
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        return response()->json(
            $this->service->update((new UserTransformer())->transform($request), $user, $this->getUser())
        );
    }
}
