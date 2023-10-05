<?php

namespace App\Http\Controllers;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Account\ContactBulkDeleteRequest;
use App\Http\Requests\Contact\ContactBulkUpdateRequest;
use App\Http\Requests\Contact\ContactGetRequest;
use App\Http\Requests\Contact\ContactStoreAttachmentRequest;
use App\Http\Requests\Contact\ContactStoreRequest;
use App\Http\Requests\Contact\ContactUpdateRequest;
use App\Http\RequestTransformers\Contact\ContactBulkUpdateTransformer;
use App\Http\RequestTransformers\Contact\ContactGetSortTransformer;
use App\Http\RequestTransformers\Contact\ContactStoreTransformer;
use App\Http\Services\ContactService;
use App\Models\Contact;
use App\Models\ContactAttachments;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function __construct(private ContactService $service)
    {
        $this->authorizeResource(Contact::class, 'contact');
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            ['bulkUpdate'],
        );
    }

    /**
     * @param ContactGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(ContactGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ContactGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ContactStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ContactStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new ContactStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     */
    public function show(Contact $contact): JsonResponse
    {
        return response()->json($this->service->show($contact));
    }

    /**
     * @param ContactUpdateRequest $request
     * @param Contact $contact
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(ContactUpdateRequest $request, Contact $contact): JsonResponse
    {
        return response()->json(
            $this->service->update((new ContactStoreTransformer())->transform($request), $contact, $this->getUser()),
        );
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $this->service->delete($contact, $this->getUser());

        return response()->json();
    }

    /**
     * @param Contact $contact
     * @param ContactStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function addAttachment(Contact $contact, ContactStoreAttachmentRequest $request): JsonResponse
    {
        $this->authorize('addAttachment', [$contact]);
        $this->service->storeAttachment($request->all(), $contact, $this->getUser());

        return response()->json();
    }

    /**
     * @param Contact $contact
     * @param ContactAttachments $attachment
     * @param ContactStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws AuthorizationException
     */
    public function updateAttachment(
        Contact $contact,
        ContactAttachments $attachment,
        ContactStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$contact, $attachment]);
        $this->service->updateAttachment($request->all(), $contact, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Contact $contact
     * @param ContactAttachments $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Contact $contact, ContactAttachments $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$contact, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }

    /**
     * @param ContactBulkDeleteRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelDeleteErrorException
     */
    public function bulkDeletes(ContactBulkDeleteRequest $request): JsonResponse
    {
        $this->authorize('deleteBulk', [Contact::class]);
        $this->service->bulkDelete($request->all(), $this->getUser());

        return response()->json();
    }

    /**
     * @param ContactBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(ContactBulkUpdateRequest $request): JsonResponse
    {
        $params = (new ContactBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }


    /**
     * @param int $contactId
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function restoreItem(int $contactId): JsonResponse
    {
        $this->authorize('restoreItem', [Contact::class]);
        $this->service->restoreItem($contactId, $this->getUser());

        return response()->json();
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(Contact $contact): JsonResponse
    {
        $this->service->getDataFromApollo($contact, $this->getUser());

        return response()->json();
    }
}
