<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\LeadType\LeadTypeGetRequest;
use App\Http\Requests\ReferenceTables\LeadType\LeadTypeStoreRequest;
use App\Http\Requests\ReferenceTables\LeadType\LeadTypeUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\LeadType\LeadTypeGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\LeadType\LeadTypeStoreTransformer;
use App\Http\Services\ReferenceTables\LeadTypeService;
use App\Models\LeadType;
use Illuminate\Http\JsonResponse;

class LeadTypeController extends Controller
{
    public function __construct(private LeadTypeService $service)
    {
        $this->authorizeResource(LeadType::class, 'leadType');
    }

    public function index(LeadTypeGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new LeadTypeGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param LeadTypeStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(LeadTypeStoreRequest $request): JsonResponse
    {
        $this->service->store((new LeadTypeStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadType $leadType
     * @return JsonResponse
     */
    public function show(LeadType $leadType): JsonResponse
    {
        return response()->json($this->service->show($leadType));
    }

    /**
     * @param LeadTypeUpdateRequest $request
     * @param LeadType $leadType
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(LeadTypeUpdateRequest $request, LeadType $leadType): JsonResponse
    {
        $this->service->update((new LeadTypeStoreTransformer())->transform($request), $leadType, $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadType $leadType
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(LeadType $leadType): JsonResponse
    {
        $this->service->delete($leadType, $this->getUser());

        return response()->json();
    }
}
