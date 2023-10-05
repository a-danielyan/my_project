<?php

namespace App\Http\Repositories;

use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProposalRepository extends BaseRepository
{
    /**
     * @param Proposal $proposal
     */
    public function __construct(Proposal $proposal)
    {
        $this->model = $proposal;
    }

    public function getForOpportunity(int $opportunityId): Model|null
    {
        return $this->model->newQuery()->where('opportunity_id', $opportunityId)->first();
    }

    public function getForAccountId(array $params): Collection|LengthAwarePaginator|array
    {
        $query = $this->model->newQuery()->whereHas('opportunity', function ($query) use ($params) {
            $query->where('account_id', $params['accountId']);
        });

        if (isset($params['limit']) && $params['limit']) {
            return $query->paginate($params['limit']);
        }

        return $query->get();
    }
}
