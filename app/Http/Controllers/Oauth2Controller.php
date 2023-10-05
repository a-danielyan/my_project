<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Http\Requests\Oauth2\Oauth2GetRequest;
use App\Http\Requests\Oauth2\Oauth2GetTokenByCodeRequest;
use App\Http\Requests\Oauth2\Oauth2ReplaceRequest;
use App\Http\Requests\Oauth2\Oauth2TokenDeleteRequest;
use App\Http\RequestTransformers\Oauth2\Oauth2GetTransformer;
use App\Http\Resource\Oauth2Resource;
use App\Http\Services\Oauth2\Oauth2ReplaceTokenService;
use App\Http\Services\Oauth2\Oauth2Service;
use App\Models\OauthToken;
use Illuminate\Http\JsonResponse;

class Oauth2Controller extends Controller
{
    private Oauth2Service $service;

    private Oauth2ReplaceTokenService $replaceService;

    /**
     * Class Constructor
     *
     * @param Oauth2Service $oauth2Service
     * @param Oauth2ReplaceTokenService $replaceService
     * @return void
     */
    public function __construct(Oauth2Service $oauth2Service, Oauth2ReplaceTokenService $replaceService)
    {
        $this->service = $oauth2Service;
        $this->replaceService = $replaceService;

        $this->authorizeResource(OauthToken::class, 'token');
    }

    public function index(Oauth2GetRequest $request): JsonResponse
    {
        return response()->json(
            Oauth2Resource::collection(
                $this->service->get(
                    (new Oauth2GetTransformer())->transform($request),
                    $this->getUser(),
                ),
            ),
        );
    }

    /**
     * @param OauthToken $token
     * @param Oauth2ReplaceRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function update(OauthToken $token, Oauth2ReplaceRequest $request): JsonResponse
    {
        $this->replaceService->replaceToken($token, $this->getUser(), $request->validated()['code']);

        return response()->json();
    }

    /**
     * @throws ModelDeleteErrorException
     */
    public function destroy(Oauth2TokenDeleteRequest $request, OauthToken $token): JsonResponse
    {
        $this->service->delete($token, $this->getUser());

        return response()->json();
    }

    /**
     * @param string $serviceType
     * @param Oauth2GetTokenByCodeRequest $request
     * @return JsonResponse
     */
    public function generateTokenByCode(string $serviceType, Oauth2GetTokenByCodeRequest $request): JsonResponse
    {
        $this->service->generateTokenByCode(
            $serviceType,
            $request->validated(),
            $this->getUser(),
        );

        return response()->json();
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            [
                'generateTokenByCode',
            ],
        );
    }
}
