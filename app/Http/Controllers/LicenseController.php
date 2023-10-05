<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\License\LicenseGetRequest;
use App\Http\Requests\License\LicenseStoreRequest;
use App\Http\Requests\License\LicenseUpdateRequest;
use App\Http\RequestTransformers\License\LicenseGetSortTransformer;
use App\Http\RequestTransformers\License\LicenseStoreTransformer;
use App\Http\Services\LicenseService;
use App\Models\License;
use Illuminate\Http\JsonResponse;

class LicenseController extends Controller
{
    public function __construct(private LicenseService $service)
    {
        $this->authorizeResource(License::class, 'license');
    }

    public function index(LicenseGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new LicenseGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param LicenseStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(LicenseStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new LicenseStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param License $license
     * @return JsonResponse
     */
    public function show(License $license): JsonResponse
    {
        return response()->json($this->service->show($license));
    }

    /**
     * @param LicenseUpdateRequest $request
     * @param License $license
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(LicenseUpdateRequest $request, License $license): JsonResponse
    {
        return response()->json(
            $this->service->update((new LicenseStoreTransformer())->transform($request), $license, $this->getUser()),
        );
    }

    /**
     * @param License $license
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(License $license): JsonResponse
    {
        $this->service->delete($license, $this->getUser());

        return response()->json();
    }
}
