<?php

namespace App\Http\Controllers;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\ConvertLeadErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Lead\LeadBulkDeleteRequest;
use App\Http\Requests\Lead\LeadBulkUpdateRequest;
use App\Http\Requests\Lead\LeadConvertRequest;
use App\Http\Requests\Lead\LeadGetRequest;
use App\Http\Requests\Lead\LeadStoreAttachmentRequest;
use App\Http\Requests\Lead\LeadStoreRequest;
use App\Http\Requests\Lead\LeadUpdateRequest;
use App\Http\RequestTransformers\Lead\LeadBulkDeleteTransformer;
use App\Http\RequestTransformers\Lead\LeadBulkUpdateTransformer;
use App\Http\RequestTransformers\Lead\LeadGetSortTransformer;
use App\Http\RequestTransformers\Lead\LeadStoreTransformer;
use App\Http\Services\LeadService;
use App\Http\Services\LeadToAccountContactConvertService;
use App\Models\Lead;
use App\Models\LeadAttachments;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    public function __construct(
        private LeadService $service,
        private LeadToAccountContactConvertService $leadToAccountContactConvertService,
    ) {
        $this->authorizeResource(Lead::class, 'lead');
    }

    /**
     * @param LeadGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(LeadGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new LeadGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param LeadStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(LeadStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new LeadStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Lead $lead
     * @return JsonResponse
     */
    public function show(Lead $lead): JsonResponse
    {
        return response()->json($this->service->show($lead));
    }

    /**
     * @param LeadUpdateRequest $request
     * @param Lead $lead
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ModelUpdateErrorException
     */
    public function update(LeadUpdateRequest $request, Lead $lead): JsonResponse
    {
        return response()->json(
            $this->service->update((new LeadStoreTransformer())->transform($request), $lead, $this->getUser()),
        );
    }

    /**
     * @param Lead $lead
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $this->service->delete($lead, $this->getUser());

        return response()->json();
    }

    /**
     * @param Lead $lead
     * @param LeadConvertRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     * @throws ConvertLeadErrorException
     */
    public function convertToContactAccount(Lead $lead, LeadConvertRequest $request): JsonResponse
    {
        return response()->json(
            $this->leadToAccountContactConvertService->convertToContactAccount(
                $lead,
                $this->getUser(),
                $request->all(),
            ),
        );
    }

    protected function resourceAbilityMap(): array
    {
        return array_merge(
            parent::resourceAbilityMap(),
            [
                'convertToContactAccount' => 'convertToContactAccount',
                'bulkDeletes' => 'bulkDeletes',
                'restoreItem' => 'restoreItem',
            ],
        );
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            ['bulkDeletes', 'restoreItem', 'bulkUpdate'],
        );
    }

    /**
     * @param LeadBulkDeleteRequest $request
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function bulkDeletes(LeadBulkDeleteRequest $request): JsonResponse
    {
        $params = (new LeadBulkDeleteTransformer())->transform($request);
        $this->service->bulkDelete($params, $this->getUser());

        return response()->json();
    }

    /**
     * @param LeadBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(LeadBulkUpdateRequest $request): JsonResponse
    {
        $params = (new LeadBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }

    /**
     * @param int $leadId
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function restoreItem(int $leadId): JsonResponse
    {
        $this->service->restoreItem($leadId, $this->getUser());

        return response()->json();
    }

    /**
     * @param Lead $lead
     * @param LeadStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function addAttachment(Lead $lead, LeadStoreAttachmentRequest $request): JsonResponse
    {
        $this->authorize('addAttachment', [$lead]);
        $this->service->storeAttachment($request->all(), $lead, $this->getUser());

        return response()->json();
    }

    /**
     * @param Lead $lead
     * @param LeadAttachments $attachment
     * @param LeadStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function updateAttachment(
        Lead $lead,
        LeadAttachments $attachment,
        LeadStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$lead, $attachment]);
        $this->service->updateAttachment($request->all(), $lead, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Lead $lead
     * @param LeadAttachments $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Lead $lead, LeadAttachments $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$lead, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    /**
     * @param Lead $lead
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(Lead $lead): JsonResponse
    {
        $this->service->getDataFromApollo($lead, $this->getUser());

        return response()->json();
    }
}
