<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Estimate\EstimateBulkDeleteRequest;
use App\Http\Requests\Estimate\EstimateBulkUpdateRequest;
use App\Http\Requests\Estimate\EstimateGetRequest;
use App\Http\Requests\Estimate\EstimateStoreAttachmentRequest;
use App\Http\Requests\Estimate\EstimateStoreRequest;
use App\Http\Requests\Estimate\EstimateUpdateRequest;
use App\Http\RequestTransformers\Estimate\EstimateBulkUpdateTransformer;
use App\Http\RequestTransformers\Estimate\EstimateGetSortTransformer;
use App\Http\RequestTransformers\Estimate\EstimateStoreTransformer;
use App\Http\Services\EstimateService;
use App\Models\Estimate;
use App\Models\EstimateAttachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class EstimateController extends Controller
{
    public function __construct(private EstimateService $service)
    {
        $this->authorizeResource(Estimate::class, 'estimate');
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            ['bulkUpdate'],
        );
    }

    /**
     * @param EstimateGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(EstimateGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new EstimateGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param EstimateStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     * @throws CustomErrorException
     */
    public function store(EstimateStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new EstimateStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Estimate $estimate
     * @return JsonResponse
     */
    public function show(Estimate $estimate): JsonResponse
    {
        return response()->json($this->service->show($estimate));
    }

    /**
     * @param EstimateUpdateRequest $request
     * @param Estimate $estimate
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ModelUpdateErrorException
     */
    public function update(EstimateUpdateRequest $request, Estimate $estimate): JsonResponse
    {
        return response()->json(
            $this->service->update((new EstimateStoreTransformer())->transform($request), $estimate, $this->getUser()),
        );
    }

    /**
     * @param Estimate $estimate
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Estimate $estimate): JsonResponse
    {
        $this->service->delete($estimate, $this->getUser());

        return response()->json();
    }

    /**
     * @param EstimateBulkDeleteRequest $request
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     * @throws AuthorizationException
     */
    public function bulkDeletes(EstimateBulkDeleteRequest $request): JsonResponse
    {
        $this->authorize('deleteBulk', [Estimate::class]);

        $this->service->bulkDelete($request->all(), $this->getUser());

        return response()->json();
    }

    /**
     * @param EstimateBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(EstimateBulkUpdateRequest $request): JsonResponse
    {
        $params = (new EstimateBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }

    public function previewEstimate(Estimate $estimate): JsonResponse
    {
        $this->service->previewEstimate($estimate);

        return response()->json();
    }

    public function generatePdf(Estimate $estimate): JsonResponse
    {
        $this->service->generatePdf($estimate);

        return response()->json();
    }

    public function createInvoice(Estimate $estimate): JsonResponse
    {
        $this->service->createInvoice($estimate);

        return response()->json();
    }

    /**
     * @param Estimate $estimate
     * @param EstimateStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function addAttachment(Estimate $estimate, EstimateStoreAttachmentRequest $request): JsonResponse
    {
        $this->service->storeAttachment($request->all(), $estimate, $this->getUser());

        return response()->json();
    }

    /**
     * @param Estimate $estimate
     * @param EstimateAttachment $attachment
     * @param EstimateStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function updateAttachment(
        Estimate $estimate,
        EstimateAttachment $attachment,
        EstimateStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$estimate, $attachment]);
        $this->service->updateAttachment($request->all(), $estimate, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Estimate $estimate
     * @param EstimateAttachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Estimate $estimate, EstimateAttachment $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$estimate, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    protected function resourceAbilityMap(): array
    {
        return array_merge(
            parent::resourceAbilityMap(),
            [
                'addAttachment' => 'addAttachment',
            ],
        );
    }
}
