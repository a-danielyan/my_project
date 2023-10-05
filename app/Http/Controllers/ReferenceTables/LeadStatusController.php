<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\LeadStatus\LeadStatusGetRequest;
use App\Http\Requests\ReferenceTables\LeadStatus\LeadStatusStoreRequest;
use App\Http\Requests\ReferenceTables\LeadStatus\LeadStatusUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\LeadStatus\LeadStatusGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\LeadStatus\LeadStatusStoreTransformer;
use App\Http\Services\ReferenceTables\LeadStatusService;
use App\Models\LeadStatus;
use Illuminate\Http\JsonResponse;

class LeadStatusController extends Controller
{
    public function __construct(private LeadStatusService $service)
    {
        $this->authorizeResource(LeadStatus::class, 'leadStatus');
    }

    public function index(LeadStatusGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new LeadStatusGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param LeadStatusStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(LeadStatusStoreRequest $request): JsonResponse
    {
        $this->service->store((new LeadStatusStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadStatus $leadStatus
     * @return JsonResponse
     */
    public function show(LeadStatus $leadStatus): JsonResponse
    {
        return response()->json($this->service->show($leadStatus));
    }

    /**
     * @param LeadStatusUpdateRequest $request
     * @param LeadStatus $leadStatus
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(LeadStatusUpdateRequest $request, LeadStatus $leadStatus): JsonResponse
    {
        $this->service->update((new LeadStatusStoreTransformer())->transform($request), $leadStatus, $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadStatus $leadStatus
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(LeadStatus $leadStatus): JsonResponse
    {
        $this->service->delete($leadStatus, $this->getUser());

        return response()->json();
    }
}
