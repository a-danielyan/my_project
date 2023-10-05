<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Payment\PaymentGetRequest;
use App\Http\Requests\Payment\PaymentStoreRequest;
use App\Http\Requests\Payment\PaymentUpdateRequest;
use App\Http\RequestTransformers\Payment\PaymentGetSortTransformer;
use App\Http\RequestTransformers\Payment\PaymentStoreTransformer;
use App\Http\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
        $this->authorizeResource(Payment::class, 'payment');
    }

    public function index(PaymentGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new PaymentGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param PaymentStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(PaymentStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new PaymentStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Payment $payment
     * @return JsonResponse
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json($this->service->show($payment));
    }

    /**
     * @param PaymentUpdateRequest $request
     * @param Payment $payment
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(PaymentUpdateRequest $request, Payment $payment): JsonResponse
    {
        return response()->json(
            $this->service->update((new PaymentStoreTransformer())->transform($request), $payment, $this->getUser()),
        );
    }
}
