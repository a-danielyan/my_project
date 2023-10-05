<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\SubscriptionGetRequest;
use App\Http\RequestTransformers\Subscription\SubscriptionGetSortTransformer;
use App\Http\Services\SubscriptionService;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service)
    {
        $this->authorizeResource(Subscription::class, 'subscription');
    }

    public function index(SubscriptionGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new SubscriptionGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function show(Subscription $subscription): JsonResponse
    {
        return response()->json($this->service->show($subscription));
    }
}
