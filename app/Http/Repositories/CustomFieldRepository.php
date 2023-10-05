<?php

namespace App\Http\Repositories;

use App\Models\CustomField;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CustomFieldRepository extends BaseRepository
{
    public function __construct(CustomField $customField)
    {
        $this->model = $customField;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)->with(['options'])
            ->paginate($params['limit']);
    }

    public function getAllowedFieldForRole(string $entityType, Role $role)
    {
        $query = $this->model->newQuery()->where('entity_type', $entityType);

        if ($role->name == Role::MAIN_ADMINISTRATOR_ROLE) {
            return $query->get(['id']);
        }

        return $query->whereHas('permissions', function ($query) {
            $query->where('action', Permission::ACTION_READ);
        })->whereHas('permissions.roleHasPermission', function ($query) use ($role) {
            $query->where('role_id', $role->getKey());
        })->get(['id']);
    }

    public function deleteAllByType(string $entityType, User $user): void
    {
        $this->model->newQuery()->where('entity_type', $entityType)
            ->update(['updated_by' => $user->getKey()]);
        $this->model->newQuery()->where('entity_type', $entityType)->delete();
    }

    public function updateOrCreate(array $where, array $data = []): Model
    {
        return $this->model->withTrashed()->updateOrCreate($where, $data);
    }

    public function getAllRequiredByType(string $entityType)
    {
        return $this->model->newQuery()->where('entity_type', $entityType)->where('is_required', true)
            ->pluck('code')->toArray();
    }

    public function getAllForEntity(string $entityType): Collection|array
    {
        return $this->model->newQuery()->where('entity_type', $entityType)->get();
    }
}
