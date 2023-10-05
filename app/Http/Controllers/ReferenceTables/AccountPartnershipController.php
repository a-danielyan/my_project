<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\AccountPartnership\AccountPartnershipGetRequest;
use App\Http\Requests\ReferenceTables\AccountPartnership\AccountPartnershipStoreRequest;
use App\Http\Requests\ReferenceTables\AccountPartnership\AccountPartnershipUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\AccountPartnership\AccountPartnershipGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\AccountPartnership\AccountPartnershipStoreTransformer;
use App\Http\Services\ReferenceTables\AccountPartnershipService;
use App\Models\AccountPartnershipStatus;
use Illuminate\Http\JsonResponse;

class AccountPartnershipController extends Controller
{
    public function __construct(private AccountPartnershipService $service)
    {
        $this->authorizeResource(AccountPartnershipStatus::class, 'accountPartnership');
    }

    public function index(AccountPartnershipGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new AccountPartnershipGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param AccountPartnershipStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(AccountPartnershipStoreRequest $request): JsonResponse
    {
        $this->service->store((new AccountPartnershipStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param AccountPartnershipStatus $accountPartnership
     * @return JsonResponse
     */
    public function show(AccountPartnershipStatus $accountPartnership): JsonResponse
    {
        return response()->json($this->service->show($accountPartnership));
    }

    /**
     * @param AccountPartnershipUpdateRequest $request
     * @param AccountPartnershipStatus $accountPartnership
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(
        AccountPartnershipUpdateRequest $request,
        AccountPartnershipStatus $accountPartnership,
    ): JsonResponse {
        $this->service->update(
            (new AccountPartnershipStoreTransformer())->transform($request),
            $accountPartnership,
            $this->getUser(),
        );

        return response()->json();
    }

    /**
     * @param AccountPartnershipStatus $accountPartnership
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(AccountPartnershipStatus $accountPartnership): JsonResponse
    {
        $this->service->delete($accountPartnership, $this->getUser());

        return response()->json();
    }
}
