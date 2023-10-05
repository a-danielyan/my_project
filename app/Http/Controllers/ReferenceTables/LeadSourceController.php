<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\LeadSource\LeadSourceGetRequest;
use App\Http\Requests\ReferenceTables\LeadSource\LeadSourceStoreRequest;
use App\Http\Requests\ReferenceTables\LeadSource\LeadSourceUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\LeadSource\LeadSourceGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\LeadSource\LeadSourceStoreTransformer;
use App\Http\Services\ReferenceTables\LeadSourceService;
use App\Models\LeadSource;
use Illuminate\Http\JsonResponse;

class LeadSourceController extends Controller
{
    public function __construct(private LeadSourceService $service)
    {
        $this->authorizeResource(LeadSource::class, 'leadSource');
    }

    public function index(LeadSourceGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new LeadSourceGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param LeadSourceStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(LeadSourceStoreRequest $request): JsonResponse
    {
        $this->service->store((new LeadSourceStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadSource $leadSource
     * @return JsonResponse
     */
    public function show(LeadSource $leadSource): JsonResponse
    {
        return response()->json($this->service->show($leadSource));
    }

    /**
     * @param LeadSourceUpdateRequest $request
     * @param LeadSource $leadSource
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(LeadSourceUpdateRequest $request, LeadSource $leadSource): JsonResponse
    {
        $this->service->update((new LeadSourceStoreTransformer())->transform($request), $leadSource, $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadSource $leadSource
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(LeadSource $leadSource): JsonResponse
    {
        $this->service->delete($leadSource, $this->getUser());

        return response()->json();
    }
}
