<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityLog\EntityLogGetRequest;
use App\Http\RequestTransformers\EntityLog\EntityLogGetSortTransformer;
use App\Http\Services\EntityLogService;
use App\Models\EntityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EntityLogController extends Controller
{
    public const ALLOWED_ENTITY = [
        'Estimate',
        'Product',
        'Opportunity',
        'Account',
        'Contact',
        'Lead',
        'Invoice',
        'Proposal',
    ];

    public function __construct(private EntityLogService $service)
    {
        $this->authorizeResource(EntityLog::class);
    }

    /**
     * @param EntityLogGetRequest $request
     * @param string $entityType
     * @param int $entityId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getLogs(EntityLogGetRequest $request, string $entityType, int $entityId): JsonResponse
    {
        Validator::make(['entityType' => $entityType], [
            'entityType' => ['required', Rule::in(self::ALLOWED_ENTITY)],
        ])->validate();

        return response()->json(
            $this->service->getAll(
                (new EntityLogGetSortTransformer())->map($request->all()),
                $entityType,
                $entityId,
            ),
        );
    }
}
