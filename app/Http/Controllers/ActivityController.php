<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Activity\ActivityGetRequest;
use App\Http\Requests\Activity\ActivityStoreRequest;
use App\Http\RequestTransformers\Activity\ActivityGetSortTransformer;
use App\Http\RequestTransformers\Activity\ActivityStoreTransformer;
use App\Http\Services\ActivityService;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function __construct(private ActivityService $service)
    {
        $this->authorizeResource(Activity::class, 'activity');
    }

    public function index(ActivityGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ActivityGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ActivityStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ActivityStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new ActivityStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Activity $activity
     * @return JsonResponse
     */
    public function show(Activity $activity): JsonResponse
    {
        return response()->json($this->service->show($activity));
    }

    /**
     * @param ActivityStoreRequest $request
     * @param Activity $activity
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(ActivityStoreRequest $request, Activity $activity): JsonResponse
    {
        return response()->json(
            $this->service->update((new ActivityStoreTransformer())->transform($request), $activity, $this->getUser()),
        );
    }

    /**
     * @param Activity $activity
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Activity $activity): JsonResponse
    {
        $this->service->delete($activity, $this->getUser());

        return response()->json();
    }
}
