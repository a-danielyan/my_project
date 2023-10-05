<?php

namespace App\Http\Services;

use App\Http\Repositories\SubjectLineRepository;
use App\Http\Resource\SubjectLineResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SubjectLineService extends BaseService
{
    public function __construct(
        SubjectLineRepository $subjectLineRepository,
    ) {
        $this->repository = $subjectLineRepository;
    }

    public function resource(): string
    {
        return SubjectLineResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        if ($user->role->name == Role::MAIN_ADMINISTRATOR_ROLE) {
            return $this->paginate($this->repository->get($user, $params));
        } else {
            return $this->paginate($this->repository->getAllForUser($user, $params));
        }
    }
}
