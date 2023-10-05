<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Opportunity\OpportunityBulkDeleteRequest;
use App\Http\Requests\Opportunity\OpportunityBulkUpdateRequest;
use App\Http\Requests\Opportunity\OpportunityGetRequest;
use App\Http\Requests\Opportunity\OpportunityStoreAttachmentRequest;
use App\Http\Requests\Opportunity\OpportunityStoreRequest;
use App\Http\Requests\Opportunity\OpportunityUpdateRequest;
use App\Http\RequestTransformers\Opportunity\OpportunityBulkUpdateTransformer;
use App\Http\RequestTransformers\Opportunity\OpportunityGetSortTransformer;
use App\Http\RequestTransformers\Opportunity\OpportunityStoreTransformer;
use App\Http\Resource\ProposalResource;
use App\Http\Resource\PublicLinkAccessLogResource;
use App\Http\Services\OpportunityService;
use App\Http\Services\ProposalService;
use App\Models\Opportunity;
use App\Models\OpportunityAttachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OpportunityController extends Controller
{
    public function __construct(
        private OpportunityService $service,
        private ProposalService $proposalService,
    ) {
        $this->authorizeResource(Opportunity::class, 'opportunity');
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            ['bulkUpdate'],
        );
    }

    /**
     * @param OpportunityGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(OpportunityGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new OpportunityGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param OpportunityStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(OpportunityStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new OpportunityStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Opportunity $opportunity
     * @return JsonResponse
     */
    public function show(Opportunity $opportunity): JsonResponse
    {
        return response()->json($this->service->show($opportunity));
    }

    /**
     * @param OpportunityUpdateRequest $request
     * @param Opportunity $opportunity
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(OpportunityUpdateRequest $request, Opportunity $opportunity): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new OpportunityStoreTransformer())->transform($request),
                $opportunity,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Opportunity $opportunity
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Opportunity $opportunity): JsonResponse
    {
        $this->service->delete($opportunity, $this->getUser());

        return response()->json();
    }


    /**
     * @param OpportunityBulkDeleteRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelDeleteErrorException
     */
    public function bulkDeletes(OpportunityBulkDeleteRequest $request): JsonResponse
    {
        $this->authorize('deleteBulk', [Opportunity::class]);

        $this->service->bulkDelete($request->all(), $this->getUser());

        return response()->json();
    }

    /**
     * @param OpportunityBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(OpportunityBulkUpdateRequest $request): JsonResponse
    {
        $params = (new OpportunityBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }

    /**
     * @param Opportunity $opportunity
     * @param OpportunityStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function addAttachment(Opportunity $opportunity, OpportunityStoreAttachmentRequest $request): JsonResponse
    {
        $this->service->storeAttachment($request->all(), $opportunity, $this->getUser());

        return response()->json();
    }

    /**
     * @param Opportunity $opportunity
     * @param OpportunityAttachment $attachment
     * @param OpportunityStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function updateAttachment(
        Opportunity $opportunity,
        OpportunityAttachment $attachment,
        OpportunityStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$opportunity, $attachment]);
        $this->service->updateAttachment($request->all(), $opportunity, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Opportunity $opportunity
     * @param OpportunityAttachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Opportunity $opportunity, OpportunityAttachment $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$opportunity, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    public function getOpportunityProposal(Opportunity $opportunity): JsonResponse
    {
        $proposal = $opportunity->proposal;
        $proposal->load([
            'estimates',
            'estimates.customFields',
            'estimates.customFields.customField',
            'estimates.opportunity.customFields',
            'estimates.opportunity.customFields.customField',
            'estimates.account.customFields',
            'estimates.account.customFields.customField',
            'estimates.contact.customFields',
            'estimates.contact.customFields.customField',
        ]);

        return response()->json(new ProposalResource($proposal));
    }

    public function getOpportunityProposalStats(Opportunity $opportunity): JsonResponse
    {
        return response()->json(
            PublicLinkAccessLogResource::collection($this->proposalService->getPublicStats($opportunity->proposal)),
        );
    }
}
