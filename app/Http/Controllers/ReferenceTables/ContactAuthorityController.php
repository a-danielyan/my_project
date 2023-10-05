<?php

namespace App\Http\Controllers\ReferenceTables;

use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTables\ContactAuthority\ContactAuthorityGetRequest;
use App\Http\Requests\ReferenceTables\ContactAuthority\ContactAuthorityStoreRequest;
use App\Http\Requests\ReferenceTables\ContactAuthority\ContactAuthorityUpdateRequest;
use App\Http\RequestTransformers\ReferenceTables\ContactAuthority\ContactAuthorityGetSortTransformer;
use App\Http\RequestTransformers\ReferenceTables\ContactAuthority\ContactAuthorityStoreTransformer;
use App\Http\Services\ReferenceTables\ContactAuthorityService;
use App\Models\ContactAuthority;
use Illuminate\Http\JsonResponse;

class ContactAuthorityController extends Controller
{
    public function __construct(private ContactAuthorityService $service)
    {
        $this->authorizeResource(ContactAuthority::class, 'contactAuthority');
    }

    public function index(ContactAuthorityGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ContactAuthorityGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ContactAuthorityStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ContactAuthorityStoreRequest $request): JsonResponse
    {
        $this->service->store((new ContactAuthorityStoreTransformer())->transform($request), $this->getUser());

        return response()->json();
    }

    /**
     * @param ContactAuthority $contactAuthority
     * @return JsonResponse
     */
    public function show(ContactAuthority $contactAuthority): JsonResponse
    {
        return response()->json($this->service->show($contactAuthority));
    }

    /**
     * @param ContactAuthorityUpdateRequest $request
     * @param ContactAuthority $contactAuthority
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(
        ContactAuthorityUpdateRequest $request,
        ContactAuthority $contactAuthority,
    ): JsonResponse {
        $this->service->update(
            (new ContactAuthorityStoreTransformer())->transform($request),
            $contactAuthority,
            $this->getUser(),
        );

        return response()->json();
    }

    /**
     * @param ContactAuthority $contactAuthority
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(ContactAuthority $contactAuthority): JsonResponse
    {
        $this->service->delete($contactAuthority, $this->getUser());

        return response()->json();
    }
}
