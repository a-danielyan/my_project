<?php

namespace App\Http\Repositories;

use App\Models\Template;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TemplateRepository extends BaseRepository
{
    /**
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->model = $template;
    }

    public function getDefaultProposalTemplate(): Model|null
    {
        return $this->model->newQuery()->where('entity', Template::TEMPLATE_TYPE_PROPOSAL)
            ->where('is_default', true)->first();
    }

    public function getAllSharedTemplates(
        array $params,
        Authenticatable|User $user,
    ): Collection|LengthAwarePaginator|array {
        $query = $this->prepareQueryForGet($params, $user);

        $query->with(['tag']);
        if (
            !isset($params['entity']) &&
            !$user->isMainAdmin() &&
            !isset($params['is_shared']) &&
            !isset($params['group'])
        ) {
            $query->where(
                function ($query) use ($user) {
                    $query->whereNotIn(
                        'entity',
                        [Template::TEMPLATE_TYPE_PROPOSAL, Template::TEMPLATE_TYPE_INVOICE],
                    )->where(function ($query) use ($user) {
                        $query->where('created_by', $user->getKey())
                            ->orWhere('is_shared', true);
                    });
                },
            );
        }

        if (isset($params['limit']) && $params['limit']) {
            return $query->paginate($params['limit']);
        }

        return $query->get();
    }
}
