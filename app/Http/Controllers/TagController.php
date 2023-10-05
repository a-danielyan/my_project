<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Tag\TagGetRequest;
use App\Http\Requests\Tag\TagStoreRequest;
use App\Http\Requests\Tag\TagUpdateRequest;
use App\Http\RequestTransformers\Tag\TagGetSortTransformer;
use App\Http\RequestTransformers\Tag\TagStoreTransformer;
use App\Http\Services\TagService;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function __construct(private TagService $service)
    {
        $this->authorizeResource(Tag::class, 'tag');
    }

    public function index(TagGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new TagGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param TagStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(TagStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new TagStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     */
    public function show(Tag $tag): JsonResponse
    {
        return response()->json($this->service->show($tag));
    }

    /**
     * @param TagUpdateRequest $request
     * @param Tag $tag
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(TagUpdateRequest $request, Tag $tag): JsonResponse
    {
        return response()->json(
            $this->service->update((new TagStoreTransformer())->transform($request), $tag, $this->getUser()),
        );
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $this->service->delete($tag, $this->getUser());

        return response()->json();
    }
}
