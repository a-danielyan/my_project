<?php

namespace App\Http\Controllers;

use App\Exceptions\CredentialInvalidErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Auth\HandleProviderCallbackRequest;
use App\Http\Requests\User\BaseUserProfileRequest;
use App\Http\RequestTransformers\Auth\AuthWithSocialTransformer;
use App\Http\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
    }

    /**
     * @param HandleProviderCallbackRequest $request
     * @return JsonResponse
     * @throws CredentialInvalidErrorException
     * @throws CustomErrorException
     */
    public function loginWithSocialUser(HandleProviderCallbackRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->loginWithSocialUser(
                (new AuthWithSocialTransformer())
                    ->transform($request)
            ),
        );
    }

    public function me(): JsonResponse
    {
        return response()->json(
            $this->service->showUserResource($this->getUser()),
        );
    }

    /**
     * @param BaseUserProfileRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(BaseUserProfileRequest $request): JsonResponse
    {
        $this->service->update($request, $this->getUser());

        return response()->json();
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request, $this->getUser());

        return response()->json();
    }

    public function refresh(): JsonResponse
    {
        return response()->json($this->service->refresh());
    }
}
