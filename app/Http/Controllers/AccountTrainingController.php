<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Account\AccountTrainingGetRequest;
use App\Http\Requests\Account\AccountTrainingStoreRequest;
use App\Http\Requests\Account\AccountTrainingUpdateRequest;
use App\Http\RequestTransformers\Account\AccountTrainingStoreTransformer;
use App\Http\Services\AccountTrainingService;
use App\Models\Account;
use App\Models\AccountTraining;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AccountTrainingController extends Controller
{
    public function __construct(private AccountTrainingService $service)
    {
        $this->authorizeResource(AccountTraining::class, 'training');
    }


    /**
     * @param AccountTrainingGetRequest $request
     * @param Account $account
     * @return JsonResponse
     */
    public function index(AccountTrainingGetRequest $request, Account $account): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                $account,
            ),
        );
    }

    /**
     * @param AccountTrainingStoreRequest $request
     * @param Account $account
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(AccountTrainingStoreRequest $request, Account $account): JsonResponse
    {
        $data = (new AccountTrainingStoreTransformer())->transform($request);
        $data['account_id'] = $account->getKey();

        return response()->json(
            $this->service->store($data, $this->getUser()),
        );
    }

    /**
     * @param Account $account
     * @param AccountTraining $training
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Account $account, AccountTraining $training): JsonResponse
    {
        $this->authorize('trainingAndAccountMatch', [$training, $account]);

        return response()->json($this->service->show($training));
    }

    /**
     * @param AccountTrainingUpdateRequest $request
     * @param Account $account
     * @param AccountTraining $training
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelUpdateErrorException
     */
    public function update(
        AccountTrainingUpdateRequest $request,
        Account $account,
        AccountTraining $training,
    ): JsonResponse {
        $this->authorize('trainingAndAccountMatch', [$training, $account]);

        return response()->json(
            $this->service->update(
                (new AccountTrainingStoreTransformer())->transform($request),
                $training,
                $this->getUser(),
            ),
        );
    }

    /**
     * @param Account $account
     * @param AccountTraining $training
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelDeleteErrorException
     */
    public function destroy(Account $account, AccountTraining $training): JsonResponse
    {
        $this->authorize('trainingAndAccountMatch', [$training, $account]);
        $this->service->delete($training, $this->getUser());

        return response()->json();
    }
}
