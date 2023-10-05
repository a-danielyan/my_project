<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Http\Requests\TermsAndConditions\TermsAndConditionGetRequest;
use App\Http\Requests\TermsAndConditions\TermsAndConditionsStoreRequest;
use App\Http\RequestTransformers\TermsAndCondition\TermsAndConditionStoreTransformer;
use App\Http\Services\TermsAndConditionService;
use App\Models\TermsAndConditions;
use Illuminate\Http\JsonResponse;

class TermsAndConditionsController extends Controller
{
    public function __construct(private TermsAndConditionService $service)
    {
        $this->authorizeResource(TermsAndConditions::class, 'terms_and_condition');
    }

    /**
     * @param TermsAndConditionGetRequest $request
     * @return JsonResponse
     */
    public function index(TermsAndConditionGetRequest $request): JsonResponse
    {
        return response()->json($this->service->getAllTerms($request->get('entity')));
    }

    /**
     * @param TermsAndConditionsStoreRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function store(TermsAndConditionsStoreRequest $request): JsonResponse
    {
        $this->service->insertTerms(
            (new TermsAndConditionStoreTransformer())->transform($request),
            $this->getUser(),
        );

        return response()->json();
    }

    public function show(TermsAndConditions $terms_and_condition): JsonResponse
    {
        return response()->json($this->service->show($terms_and_condition));
    }

    public function update(
        TermsAndConditionsStoreRequest $request,
        TermsAndConditions $terms_and_condition,
    ): JsonResponse {
        $this->service->updatePreference(
            (new TermsAndConditionStoreTransformer())->transform($request),
            $this->getUser(),
            $terms_and_condition,
        );

        return response()->json();
    }

    /**
     * @param TermsAndConditions $terms_and_condition
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(TermsAndConditions $terms_and_condition): JsonResponse
    {
        $this->service->delete($terms_and_condition, $this->getUser());

        return response()->json();
    }
}
