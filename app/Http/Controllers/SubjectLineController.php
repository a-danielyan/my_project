<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\SubjectLine\SubjectLineGetRequest;
use App\Http\Requests\SubjectLine\SubjectLineStoreRequest;
use App\Http\RequestTransformers\SubjectLine\SubjectLineGetSortTransformer;
use App\Http\RequestTransformers\SubjectLine\SubjectLineStoreTransformer;
use App\Http\Services\SubjectLineService;
use App\Models\SubjectLine;
use Illuminate\Http\JsonResponse;

class SubjectLineController extends Controller
{
    public function __construct(private SubjectLineService $service)
    {
        $this->authorizeResource(SubjectLine::class, 'subject_line');
    }

    public function index(SubjectLineGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SubjectLineGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SubjectLineStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(SubjectLineStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new SubjectLineStoreTransformer())->transform($request), $this->getUser()),
        );
    }


    public function show(SubjectLine $subjectLine): JsonResponse
    {
        return response()->json($this->service->show($subjectLine));
    }

    /**
     * @param SubjectLineStoreRequest $request
     * @param SubjectLine $subjectLine
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(SubjectLineStoreRequest $request, SubjectLine $subjectLine): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new SubjectLineStoreTransformer())->transform($request),
                $subjectLine,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SubjectLine $subjectLine
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(SubjectLine $subjectLine): JsonResponse
    {
        $this->service->delete($subjectLine, $this->getUser());

        return response()->json();
    }
}
