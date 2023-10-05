<?php

namespace App\Http\Services;

use App\Exceptions\ModelDeleteErrorException;
use App\Http\Repositories\PreferenceRepository;
use App\Http\Resource\PreferenceResource;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PreferencesService extends BaseService
{
    public function __construct(
        PreferenceRepository $preferenceRepository,
    ) {
        $this->repository = $preferenceRepository;
    }

    public function getAllPreference(string $entity, User|Authenticatable $user)
    {
        $data = $this->repository->get(where: ['user_id' => $user->getKey(), 'entity' => $entity]);

        $resource = $this->resource();

        return $resource::collection($data);
    }


    public function insertPreference(array $data, User|Authenticatable $user)
    {
        $data['user_id'] = $user->getKey();
        $preference = $this->repository->create($data);
        $resource = $this->resource();

        return new $resource($preference);
    }

    public function updatePreference(array $data, User|Authenticatable $user, Preference $preference)
    {
        $data['user_id'] = $user->getKey();
        $this->repository->update($preference, $data);
        $resource = $this->resource();

        return new $resource($preference);
    }


    public function resource(): string
    {
        return PreferenceResource::class;
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
