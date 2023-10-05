<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\CustomField\CustomFieldBulkUpdateRequest;
use App\Http\Requests\CustomField\CustomFieldGetRequest;
use App\Http\Requests\CustomField\CustomFieldStoreRequest;
use App\Http\Requests\CustomField\CustomFieldUpdateRequest;
use App\Http\RequestTransformers\CustomField\CustomFieldBulkUpdateTransformer;
use App\Http\RequestTransformers\CustomField\CustomFieldGetSortTransformer;
use App\Http\RequestTransformers\CustomField\CustomFieldStoreTransformer;
use App\Http\Services\CustomFieldService;
use App\Models\CustomField;
use Illuminate\Http\JsonResponse;

class CustomFieldController extends Controller
{
    public function __construct(private CustomFieldService $service)
    {
        $this->authorizeResource(CustomField::class, 'customField');
    }

    protected function resourceAbilityMap(): array
    {
        $parentResource = parent::resourceAbilityMap();
        unset($parentResource['bulkUpdate']);

        return $parentResource;
    }



    /**
     * @param CustomFieldGetRequest $request
     * @return JsonResponse
     */
    public function index(CustomFieldGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new CustomFieldGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param CustomFieldStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(CustomFieldStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new CustomFieldStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param CustomField $customField
     * @return JsonResponse
     */
    public function show(CustomField $customField): JsonResponse
    {
        return response()->json($this->service->show($customField));
    }

    /**
     * @param CustomFieldUpdateRequest $request
     * @param CustomField $customField
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(CustomFieldUpdateRequest $request, CustomField $customField): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new CustomFieldStoreTransformer())->transform($request),
                $customField,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param CustomField $customField
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(CustomField $customField): JsonResponse
    {
        $this->service->delete($customField, $this->getUser());

        return response()->json();
    }

    /**
     * @param CustomFieldBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(CustomFieldBulkUpdateRequest $request): JsonResponse
    {
        $this->service->bulkUpdate(
            (new CustomFieldBulkUpdateTransformer())->transform($request),
            $this->getUser(),
        );

        return response()->json();
    }

    public function getSettings(): JsonResponse
    {
        return response()->json(
            [
                'availableCustomFieldTypes' => CustomField::AVAILABLE_ENTITY_TYPES,
                'lookupType' => CustomField::AVAILABLE_LOOKUP_TYPES,
            ],
        );
    }
}
