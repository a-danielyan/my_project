<?php

namespace App\Http\Services\Oauth2;

use App\Exceptions\ModelDeleteErrorException;
use App\Http\Middleware\Authenticate;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\Oauth2\Oauth2Repository;
use App\Http\Resource\Oauth2Resource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Oauth2Service extends BaseService
{
    protected BaseRepository $repository;

    public function __construct(
        Oauth2Repository $oauth2Repository,
    ) {
        $this->repository = $oauth2Repository;
    }

    public function get(array $params, User $user = null)
    {
        return $this->repository->getTokens(
            $user,
            $params['service'],
        );
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

    /**
     * @param string $serviceName
     * @param array $params
     * @param User|Authenticate $user
     * @return string
     */
    public function generateTokenByCode(string $serviceName, array $params, User|Authenticatable $user): string
    {
        $oauth2Service = Oauth2ServiceFactory::createService($serviceName, $user);

        $oauth2TokenService = resolve(Oauth2TokenService::class, [
            'oauth2Service' => $oauth2Service,
            'serviceName' => $serviceName,
        ]);

        return $oauth2TokenService->getTokenByCode($params, $user, $params['userName'] ?? 'default');
    }

    public function resource(): string
    {
        return Oauth2Resource::class;
    }
}
