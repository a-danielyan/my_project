<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Template\TemplateGetRequest;
use App\Http\Requests\Template\TemplateStoreRequest;
use App\Http\Requests\Template\TemplateUpdateRequest;
use App\Http\RequestTransformers\Template\TemplateGetSortTransformer;
use App\Http\RequestTransformers\Template\TemplateStoreTransformer;
use App\Http\Services\TemplateService;
use App\Models\Template;
use Illuminate\Http\JsonResponse;

class TemplateController extends Controller
{
    public function __construct(private TemplateService $service)
    {
        $this->authorizeResource(Template::class, 'template');
    }

    public function index(TemplateGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new TemplateGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param TemplateStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(TemplateStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new TemplateStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Template $template
     * @return JsonResponse
     */
    public function show(Template $template): JsonResponse
    {
        return response()->json($this->service->show($template));
    }

    /**
     * @param TemplateUpdateRequest $request
     * @param Template $template
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(TemplateUpdateRequest $request, Template $template): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new TemplateStoreTransformer())->transform($request),
                $template,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Template $template
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Template $template): JsonResponse
    {
        $this->service->delete($template, $this->getUser());

        return response()->json();
    }
}
