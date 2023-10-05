<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Sequence\SequenceGetRequest;
use App\Http\Requests\Sequence\SequenceStoreRequest;
use App\Http\Requests\Sequence\SequenceUpdateRequest;
use App\Http\RequestTransformers\Sequence\SequenceGetSortTransformer;
use App\Http\RequestTransformers\Sequence\SequenceStoreTransformer;
use App\Http\Services\SequenceService;
use App\Models\Sequence\Sequence;
use Illuminate\Http\JsonResponse;

class SequenceController extends Controller
{
    public function __construct(private SequenceService $service)
    {
        $this->authorizeResource(Sequence::class, 'sequence');
    }

    /**
     * @param SequenceGetRequest $request
     * @return JsonResponse
     */
    public function index(SequenceGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SequenceGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SequenceStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(SequenceStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new SequenceStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Sequence $sequence
     * @return JsonResponse
     */
    public function show(Sequence $sequence): JsonResponse
    {
        return response()->json($this->service->show($sequence));
    }

    /**
     * @param SequenceUpdateRequest $request
     * @param Sequence $sequence
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(SequenceUpdateRequest $request, Sequence $sequence): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new SequenceStoreTransformer())->transform($request),
                $sequence,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Sequence $sequence
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Sequence $sequence): JsonResponse
    {
        $this->service->delete($sequence, $this->getUser());

        return response()->json();
    }
}
