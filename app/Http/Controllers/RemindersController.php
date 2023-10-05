<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Reminder\ReminderGetRequest;
use App\Http\Requests\Reminder\ReminderStoreRequest;
use App\Http\Requests\Reminder\ReminderUpdateRequest;
use App\Http\RequestTransformers\Reminder\ReminderGetSortTransformer;
use App\Http\RequestTransformers\Reminder\ReminderStoreTransformer;
use App\Http\Services\RemindersService;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;

class RemindersController extends Controller
{
    public function __construct(private RemindersService $service)
    {
        $this->authorizeResource(Reminder::class, 'reminder');
    }

    /**
     * @param ReminderGetRequest $request
     * @return JsonResponse
     */
    public function index(ReminderGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ReminderGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ReminderStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ReminderStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new ReminderStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Reminder $reminder
     * @return JsonResponse
     */
    public function show(Reminder $reminder): JsonResponse
    {
        return response()->json($this->service->show($reminder));
    }

    /**
     * @param ReminderUpdateRequest $request
     * @param Reminder $reminder
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(ReminderUpdateRequest $request, Reminder $reminder): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new ReminderStoreTransformer())->transform($request),
                $reminder,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Reminder $reminder
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Reminder $reminder): JsonResponse
    {
        $this->service->delete($reminder, $this->getUser());

        return response()->json();
    }
}
