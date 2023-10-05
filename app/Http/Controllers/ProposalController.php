<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelUpdateErrorException;
use App\Exceptions\NotFoundException;
use App\Http\Requests\Proposal\ProposalGetRequest;
use App\Http\Requests\Proposal\ProposalUpdateRequest;
use App\Http\RequestTransformers\Proposal\ProposalGetSortTransformer;
use App\Http\RequestTransformers\Proposal\ProposalStoreTransformer;
use App\Http\Resource\ProposalResource;
use App\Http\Services\ProposalService;
use App\Models\Proposal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function __construct(private ProposalService $service)
    {
        $this->authorizeResource(Proposal::class, 'proposal');
    }


    public function index(ProposalGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ProposalGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ProposalUpdateRequest $request
     * @param Proposal $proposal
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(ProposalUpdateRequest $request, Proposal $proposal): JsonResponse
    {
        return response()->json(
            $this->service->update((new ProposalStoreTransformer())->transform($request), $proposal, $this->getUser()),
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundException
     */
    public function getPublicProposal(Request $request): JsonResponse
    {
        return response()->json(
            new ProposalResource(
                $this->service->getPublicProposalData(
                    $request->all(),
                ),
            ),
        );
    }
}
