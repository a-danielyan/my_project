<?php

namespace App\Http\Controllers;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Account\AccountBulkDeleteRequest;
use App\Http\Requests\Account\AccountBulkUpdateRequest;
use App\Http\Requests\Account\AccountGetRequest;
use App\Http\Requests\Account\AccountStoreAttachmentRequest;
use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Http\RequestTransformers\Account\AccountBulkUpdateTransformer;
use App\Http\RequestTransformers\Account\AccountGetSortTransformer;
use App\Http\RequestTransformers\Account\AccountStoreTransformer;
use App\Http\Services\AccountService;
use App\Models\Account;
use App\Models\AccountAttachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function __construct(private AccountService $service)
    {
        $this->authorizeResource(Account::class, 'account');
    }

    protected function resourceAbilityMap(): array
    {
        return array_merge(
            parent::resourceAbilityMap(),
            [
                'bulkDeletes' => 'bulkDeletes',
                'restoreItem' => 'restoreItem',
                'addAttachment' => 'addAttachment',
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
     * @param AccountGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(AccountGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new AccountGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param AccountStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(AccountStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new AccountStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Account $account
     * @return JsonResponse
     */
    public function show(Account $account): JsonResponse
    {
        return response()->json($this->service->show($account));
    }

    /**
     * @param AccountUpdateRequest $request
     * @param Account $account
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(AccountUpdateRequest $request, Account $account): JsonResponse
    {
        return response()->json(
            $this->service->update((new AccountStoreTransformer())->transform($request), $account, $this->getUser()),
        );
    }

    /**
     * @param Account $account
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Account $account): JsonResponse
    {
        $this->service->delete($account, $this->getUser());

        return response()->json();
    }

    /**
     * @param Account $account
     * @param AccountStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function addAttachment(Account $account, AccountStoreAttachmentRequest $request): JsonResponse
    {
        $this->service->storeAttachment($request->all(), $account, $this->getUser());

        return response()->json();
    }

    /**
     * @param Account $account
     * @param AccountAttachment $attachment
     * @param AccountStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function updateAttachment(
        Account $account,
        AccountAttachment $attachment,
        AccountStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$account, $attachment]);
        $this->service->updateAttachment($request->all(), $account, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Account $account
     * @param AccountAttachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Account $account, AccountAttachment $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$account, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    /**
     * @param AccountBulkDeleteRequest $request
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function bulkDeletes(AccountBulkDeleteRequest $request): JsonResponse
    {
        $this->service->bulkDelete($request->all(), $this->getUser());

        return response()->json();
    }

    /**
     * @param AccountBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(AccountBulkUpdateRequest $request): JsonResponse
    {
        $params = (new AccountBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }

    /**
     * @param int $accountId
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function restoreItem(int $accountId): JsonResponse
    {
        $this->service->restoreItem($accountId, $this->getUser());

        return response()->json();
    }

    /**
     * @param Account $account
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(Account $account): JsonResponse
    {
        $this->service->getDataFromApollo($account, $this->getUser());

        return response()->json();
    }
}
