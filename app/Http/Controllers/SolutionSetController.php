<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\SolutionSet\SolutionSetGetRequest;
use App\Http\Requests\SolutionSet\SolutionSetStoreRequest;
use App\Http\Requests\SolutionSet\SolutionSetUpdateRequest;
use App\Http\RequestTransformers\SolutionSet\SolutionSetGetSortTransformer;
use App\Http\RequestTransformers\SolutionSet\SolutionSetStoreTransformer;
use App\Http\Services\SolutionSetService;
use App\Models\SolutionSet;
use Illuminate\Http\JsonResponse;

class SolutionSetController extends Controller
{
    public function __construct(private SolutionSetService $service)
    {
        $this->authorizeResource(SolutionSet::class, 'solution_set');
    }

    /**
     * @param SolutionSetGetRequest $request
     * @return JsonResponse
     */
    public function index(SolutionSetGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SolutionSetGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SolutionSetStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(SolutionSetStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new SolutionSetStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param SolutionSet $solution_set
     * @return JsonResponse
     */
    public function show(SolutionSet $solution_set): JsonResponse
    {
        return response()->json($this->service->show($solution_set));
    }

    /**
     * @param SolutionSetUpdateRequest $request
     * @param SolutionSet $solution_set
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(SolutionSetUpdateRequest $request, SolutionSet $solution_set): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new SolutionSetStoreTransformer())->transform($request),
                $solution_set,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SolutionSet $solution_set
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(SolutionSet $solution_set): JsonResponse
    {
        $this->service->delete($solution_set, $this->getUser());

        return response()->json();
    }
}
