<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\Industry\IndustryGetRequest;
use App\Http\Requests\ReferenceTables\Industry\IndustryStoreRequest;
use App\Http\Requests\ReferenceTables\Industry\IndustryUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\Industry\IndustryGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\Industry\IndustryStoreTransformer;
use App\Http\Services\ReferenceTables\IndustryService;
use App\Models\Industry;
use Illuminate\Http\JsonResponse;

class IndustryController extends Controller
{
    public function __construct(private IndustryService $service)
    {
        $this->authorizeResource(Industry::class, 'industry');
    }

    public function index(IndustryGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new IndustryGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param IndustryStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(IndustryStoreRequest $request): JsonResponse
    {
        $this->service->store((new IndustryStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param Industry $industry
     * @return JsonResponse
     */
    public function show(Industry $industry): JsonResponse
    {
        return response()->json($this->service->show($industry));
    }

    /**
     * @param IndustryUpdateRequest $request
     * @param Industry $industry
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(IndustryUpdateRequest $request, Industry $industry): JsonResponse
    {
        $this->service->update((new IndustryStoreTransformer())->transform($request), $industry, $this->getUser());

        return response()->json();
    }

    /**
     * @param Industry $industry
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Industry $industry): JsonResponse
    {
        $this->service->delete($industry, $this->getUser());

        return response()->json();
    }
}
