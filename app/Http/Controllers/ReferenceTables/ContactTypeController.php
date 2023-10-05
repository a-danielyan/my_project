<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\ContactType\ContactTypeGetRequest;
use App\Http\Requests\ReferenceTables\ContactType\ContactTypeStoreRequest;
use App\Http\Requests\ReferenceTables\ContactType\ContactTypeUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\ContactType\ContactTypeGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\ContactType\ContactTypeStoreTransformer;
use App\Http\Services\ReferenceTables\ContactTypeService;
use App\Models\ContactType;
use Illuminate\Http\JsonResponse;

class ContactTypeController extends Controller
{
    public function __construct(private ContactTypeService $service)
    {
        $this->authorizeResource(ContactType::class, 'contactType');
    }

    public function index(ContactTypeGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ContactTypeGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ContactTypeStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ContactTypeStoreRequest $request): JsonResponse
    {
        $this->service->store((new ContactTypeStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param ContactType $contactType)
     * @return JsonResponse
     */
    public function show(ContactType $contactType): JsonResponse
    {
        return response()->json($this->service->show($contactType));
    }

    /**
     * @param ContactTypeUpdateRequest $request
     * @param ContactType $contactType
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(
        ContactTypeUpdateRequest $request,
        ContactType $contactType,
    ): JsonResponse {
        $this->service->update(
            (new ContactTypeStoreTransformer())->transform($request),
            $contactType,
            $this->getUser(),
        );

        return response()->json();
    }

    /**
     * @param ContactType $contactType
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(ContactType $contactType): JsonResponse
    {
        $this->service->delete($contactType, $this->getUser());

        return response()->json();
    }
}
