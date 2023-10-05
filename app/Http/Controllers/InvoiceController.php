<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\AuthorizeNetTransactionRequest;
use App\Http\Requests\Invoice\InvoiceGetRequest;
use App\Http\Requests\Invoice\InvoiceStoreAttachmentRequest;
use App\Http\Requests\Invoice\InvoiceStoreRequest;
use App\Http\Requests\Invoice\InvoiceUpdateRequest;
use App\Http\RequestTransformers\Invoice\InvoiceGetSortTransformer;
use App\Http\RequestTransformers\Invoice\InvoiceStoreTransformer;
use App\Http\RequestTransformers\Invoice\InvoiceUpdateTransformer;
use App\Http\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Stripe\Exception\ApiErrorException;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $service)
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    public function index(InvoiceGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new InvoiceGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($this->service->show($invoice));
    }

    /**
     * @param InvoiceStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(InvoiceStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new InvoiceStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param InvoiceUpdateRequest $request
     * @param Invoice $invoice
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice): JsonResponse
    {
        return response()->json(
            $this->service->update((new InvoiceUpdateTransformer())->transform($request), $invoice, $this->getUser()),
        );
    }

    /**
     * @param Invoice $invoice
     * @param InvoiceStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function addAttachment(Invoice $invoice, InvoiceStoreAttachmentRequest $request): JsonResponse
    {
        $this->authorize('addAttachment', [$invoice]);
        $this->service->storeAttachment($request->all(), $invoice, $this->getUser());

        return response()->json();
    }

    /**
     * @param Invoice $invoice
     * @param InvoiceAttachment $attachment
     * @param InvoiceStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function updateAttachment(
        Invoice $invoice,
        InvoiceAttachment $attachment,
        InvoiceStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$invoice, $attachment]);

        $this->service->updateAttachment($request->all(), $invoice, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Invoice $invoice
     * @param InvoiceAttachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Invoice $invoice, InvoiceAttachment $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$invoice, $attachment]);

        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    /**
     * @param Invoice $invoice
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     * @throws ApiErrorException
     */
    public function getStripeClientSecret(Invoice $invoice): JsonResponse
    {
        $this->authorize('view', [$invoice]);

        return response()->json($this->service->getStripeClientSecret($invoice));
    }

    /**
     * @param AuthorizeNetTransactionRequest $request
     * @param Invoice $invoice
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAuthorizeTransactionDetails(
        AuthorizeNetTransactionRequest $request,
        Invoice $invoice,
    ): JsonResponse {
        $this->authorize('view', [$invoice]);

        return response()->json($this->service->getAuthorizeTransactionDetails($invoice, $request->validated()));
    }

    /**
     * @param Invoice $invoice
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function sendInvoice(Invoice $invoice): JsonResponse
    {
        $this->authorize('view', [$invoice]);
        $this->service->sendInvoice($invoice);

        return response()->json();
    }
}
