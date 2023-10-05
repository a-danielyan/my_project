<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Account\AccountDemoGetRequest;
use App\Http\Requests\Account\AccountDemoStoreRequest;
use App\Http\Requests\Account\AccountDemoUpdateRequest;
use App\Http\RequestTransformers\Account\AccountDemoStoreTransformer;
use App\Http\Services\AccountDemoService;
use App\Models\Account;
use App\Models\AccountDemo;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AccountDemoController extends Controller
{
    public function __construct(private AccountDemoService $service)
    {
        $this->authorizeResource(AccountDemo::class, 'demo');
    }

    /**
     * @param AccountDemoGetRequest $request
     * @param Account $account
     * @return JsonResponse
     */
    public function index(AccountDemoGetRequest $request, Account $account): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                $account,
            ),
        );
    }

    /**
     * @param AccountDemoStoreRequest $request
     * @param Account $account
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(AccountDemoStoreRequest $request, Account $account): JsonResponse
    {
        $data = (new AccountDemoStoreTransformer())->transform($request);
        $data['account_id'] = $account->getKey();

        return response()->json(
            $this->service->store($data, $this->getUser()),
        );
    }

    /**
     * @param Account $account
     * @param AccountDemo $demo
     * @return JsonResponse
     */
    /**
     * @param Account $account
     * @param AccountDemo $demo
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Account $account, AccountDemo $demo): JsonResponse
    {
        $this->authorize('demoAndAccountMatch', [$demo, $account]);

        return response()->json($this->service->show($demo));
    }

    /**
     * @param AccountDemoUpdateRequest $request
     * @param Account $account
     * @param AccountDemo $demo
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelUpdateErrorException
     */
    public function update(AccountDemoUpdateRequest $request, Account $account, AccountDemo $demo): JsonResponse
    {
        $this->authorize('demoAndAccountMatch', [$demo, $account]);
        return response()->json(
            $this->service->update(
                (new AccountDemoStoreTransformer())->transform($request),
                $demo,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Account $account
     * @param AccountDemo $demo
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelDeleteErrorException
     */
    public function destroy(Account $account, AccountDemo $demo): JsonResponse
    {
        $this->authorize('demoAndAccountMatch', [$demo, $account]);
        $this->service->delete($demo, $this->getUser());

        return response()->json();
    }
}
