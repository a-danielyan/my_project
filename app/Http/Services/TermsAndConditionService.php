<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Http\Repositories\TermsAndConditionRepository;
use App\Http\Resource\TermsAndConditionsResource;
use App\Models\TermsAndConditions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TermsAndConditionService extends BaseService
{
    public function __construct(
        TermsAndConditionRepository $termsAndConditionRepository,
    ) {
        $this->repository = $termsAndConditionRepository;
    }

    public function getAllTerms(string $entity)
    {
        $data = $this->repository->get(where: ['entity' => $entity]);

        $resource = $this->resource();

        return $resource::collection($data);
    }

    /**
     * @param array $data
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function insertTerms(array $data, User|Authenticatable $user): void
    {
        $data['created_by'] = $user->getKey();
        $countTerms = $this->repository->getCountByParams(where: ['entity' => $data['entity']]);
        if ($countTerms > 0) {
            throw new CustomErrorException(
                'We already have terms for ' . $data['entity'] . ' please update them',
                422
            );
        }
        $this->repository->create($data);
    }

    public function updatePreference(
        array $data,
        User|Authenticatable $user,
        TermsAndConditions $termsAndCondition,
    ): void {
        $data['updated_by'] = $user->getKey();
        $this->repository->update($termsAndCondition, $data);
    }


    public function resource(): string
    {
        return TermsAndConditionsResource::class;
    }

    /**
     * @param Model $model
     * @param Authenticatable $user
     * @return bool
     * @throws ModelDeleteErrorException
     */
    public function delete(Model $model, Authenticatable $user): bool
    {
        if ($this->repository->delete($model)) {
            return true;
        }

        throw new ModelDeleteErrorException();
    }
}
