<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\PaymentProfile\PaymentProfileGetRequest;
use App\Http\Requests\PaymentProfile\PaymentProfileStoreRequest;
use App\Http\Requests\PaymentProfile\PaymentProfileUpdateRequest;
use App\Http\RequestTransformers\PaymentProfile\PaymentProfileGetSortTransformer;
use App\Http\RequestTransformers\PaymentProfile\PaymentProfileStoreTransformer;
use App\Http\Services\PaymentProfileService;
use App\Models\PaymentProfile;
use Illuminate\Http\JsonResponse;

class PaymentProfileController extends Controller
{
    public function __construct(private PaymentProfileService $service)
    {
        $this->authorizeResource(PaymentProfile::class, 'payment_profile');
    }

    public function index(PaymentProfileGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new PaymentProfileGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param PaymentProfileStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(PaymentProfileStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new PaymentProfileStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param PaymentProfile $payment_profile
     * @return JsonResponse
     */
    public function show(PaymentProfile $payment_profile): JsonResponse
    {
        return response()->json($this->service->show($payment_profile));
    }

    /**
     * @param PaymentProfileUpdateRequest $request
     * @param PaymentProfile $payment_profile
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(PaymentProfileUpdateRequest $request, PaymentProfile $payment_profile): JsonResponse
    {
        return response()->json(
            $this->service->update(
                (new PaymentProfileStoreTransformer())->transform($request),
                $payment_profile,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param PaymentProfile $payment_profile
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(PaymentProfile $payment_profile): JsonResponse
    {
        $this->service->delete($payment_profile, $this->getUser());

        return response()->json();
    }
}
