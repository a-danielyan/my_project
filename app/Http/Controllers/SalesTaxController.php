<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\SalesTax\SalesTaxGetRequest;
use App\Http\Requests\SalesTax\SalesTaxStoreRequest;
use App\Http\Requests\SalesTax\SalesTaxUpdateRequest;
use App\Http\RequestTransformers\SalesTax\SalesTaxGetSortTransformer;
use App\Http\RequestTransformers\SalesTax\SalesTaxStoreTransformer;
use App\Http\Services\SalesTaxService;
use App\Models\SalesTax;
use Illuminate\Http\JsonResponse;

class SalesTaxController extends Controller
{
    public function __construct(private SalesTaxService $service)
    {
        $this->authorizeResource(SalesTax::class, 'sales_tax');
    }

    public function index(SalesTaxGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SalesTaxGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SalesTaxStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(SalesTaxStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new SalesTaxStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param SalesTax $salesTax
     * @return JsonResponse
     */
    public function show(SalesTax $salesTax): JsonResponse
    {
        return response()->json($this->service->show($salesTax));
    }

    /**
     * @param SalesTaxUpdateRequest $request
     * @param SalesTax $salesTax
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(SalesTaxUpdateRequest $request, SalesTax $salesTax): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new SalesTaxStoreTransformer())->transform($request),
                $salesTax,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param SalesTax $salesTax
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(SalesTax $salesTax): JsonResponse
    {
        $this->service->delete($salesTax, $this->getUser());

        return response()->json();
    }
}
