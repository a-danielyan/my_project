<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\Stage\StageGetRequest;
use App\Http\Requests\ReferenceTables\Stage\StageStoreRequest;
use App\Http\Requests\ReferenceTables\Stage\StageUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\Stage\StageGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\Stage\StageStoreTransformer;
use App\Http\Services\ReferenceTables\StageService;
use App\Models\Stage;
use Illuminate\Http\JsonResponse;

class StageController extends Controller
{
    public function __construct(private StageService $service)
    {
        $this->authorizeResource(Stage::class, 'stage');
    }

    public function index(StageGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new StageGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param StageStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(StageStoreRequest $request): JsonResponse
    {
        $this->service->store((new StageStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }


    public function show(Stage $stage): JsonResponse
    {
        return response()->json($this->service->show($stage));
    }

    /**
     * @param StageUpdateRequest $request
     * @param Stage $stage
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(StageUpdateRequest $request, Stage $stage): JsonResponse
    {
        $this->service->update((new StageStoreTransformer())->transform($request), $stage, $this->getUser());

        return response()->json();
    }

    /**
     * @param Stage $stage
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Stage $stage): JsonResponse
    {
        $this->service->delete($stage, $this->getUser());

        return response()->json();
    }
}
