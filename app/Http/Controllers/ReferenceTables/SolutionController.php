<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\Solution\SolutionGetRequest;
use App\Http\Requests\ReferenceTables\Solution\SolutionStoreRequest;
use App\Http\Requests\ReferenceTables\Solution\SolutionUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\Solution\SolutionGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\Solution\SolutionStoreTransformer;
use App\Http\Services\ReferenceTables\SolutionService;
use App\Models\Solutions;
use Illuminate\Http\JsonResponse;

class SolutionController extends Controller
{
    public function __construct(private SolutionService $service)
    {
        $this->authorizeResource(Solutions::class, 'solution');
    }

    public function index(SolutionGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SolutionGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SolutionStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(SolutionStoreRequest $request): JsonResponse
    {
        $this->service->store((new SolutionStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }


    public function show(Solutions $solution): JsonResponse
    {
        return response()->json($this->service->show($solution));
    }

    /**
     * @param SolutionUpdateRequest $request
     * @param Solutions $solution
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(SolutionUpdateRequest $request, Solutions $solution): JsonResponse
    {
        $this->service->update((new SolutionStoreTransformer())->transform($request), $solution, $this->getUser());

        return response()->json();
    }

    /**
     * @param Solutions $solution
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Solutions $solution): JsonResponse
    {
        $this->service->delete($solution, $this->getUser());

        return response()->json();
    }
}
