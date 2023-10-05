<?php

namespace App\Http\Services\Auth;

use App\Exceptions\CredentialInvalidErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\User\BaseUserProfileRequest;
use App\Http\RequestTransformers\Auth\AuthUpdateTransformer;
use App\Http\Resource\AuthResource;
use App\Http\Services\User\LogService;
use App\Models\Role;
use App\Models\User;
use Google_Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class AuthService
 */
class AuthService
{
    public function __construct(
        private UserRepository $repository,
        private LogService $logService,
    ) {
    }

    /**
     * Function returns email string
     *
     * @return string
     */
    public function username(): string
    {
        return 'email';
    }

    /**
     * Logout request
     *
     * @param Request $request
     * @param User|Authenticatable $user
     * @return bool
     */
    public function logout(Request $request, User|Authenticatable $user): bool
    {
        $this->logService->logoutLog(Arr::last($request->getClientIps()), $user);

        auth()->logout();

        return true;
    }

    /**
     * Refresh token
     *
     * @return array
     */
    public function refresh(): array
    {
        return $this->respondWithToken(auth()->refresh());
    }


    /**
     * System user respond with token
     *
     * @param string $token
     * @return array
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Handle the call back for user
     *
     * @param array $data
     * @return array
     * @throws CredentialInvalidErrorException
     * @throws CustomErrorException
     */
    public function loginWithSocialUser(array $data): array
    {
        $userData = $this->getSocialUserData($data);
        $user = $this->repository->findActiveUserByEmail($userData['email']);
        if (!$user) {
            $standardRole = Role::query()->where('name', Role::STANDARD_USER_ROLE)->first();
            $this->repository->create([
                'first_name' => $userData['raw']['given_name'] ?? '',
                'last_name' => $userData['raw']['family_name'] ?? '',
                'avatar' => $userData['avatar'] ?? '',
                'email' => $userData['email'],
                'status' => User::STATUS_INACTIVE,
                'role_id' => $standardRole->getKey(),
            ]);
            throw new CustomErrorException('User not Active', 422);
        }

        if ($user['status'] === User::STATUS_INACTIVE) {
            throw new CustomErrorException('User not Active', 422);
        }
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        if ($token = auth()->guard()->login($user)) {
            if (!empty($userData['avatar'])) {
                $this->repository->update($user, ['avatar' => $userData['avatar']]);
            }
            $this->logService->loginLog(request()->getClientIp(), $user);

            return $this->respondWithToken($token);
        }

        throw new CredentialInvalidErrorException();
    }

    protected function getSocialUserData(array $data): array
    {
        $provider = $data['provider'] ?? '';
        $token = $data['token'] ?? null;
        $code = $data['code'] ?? null;

        if ($provider === 'local' && (app()->isLocal() || app()->runningUnitTests())) {
            return ['email' => $data['token']];
        }

        if ($data['provider'] == 'google' && isset($data['tokenId'])) {
            return $this->getGoogleSocialUserData($data);
        }

        if ($code) {
            $responseToken = Socialite::with($provider)->stateless()->getAccessTokenResponse($code);

            $token = Arr::get($responseToken, 'access_token');
        }

        /**
         * @var AbstractUser $providerUser
         */
        $providerUser = Socialite::with($provider)->stateless()->userFromToken($token);

        /** @noinspection PhpUndefinedFieldInspection */
        return [
            'id' => $providerUser->getId(),
            'email' => $provider === 'graph' ? $providerUser->userPrincipalName : $providerUser->getEmail(),
            'name' => $providerUser->getName(),
            'avatar' => $providerUser->getAvatar(),
            'raw' => $providerUser->getRaw(),
        ];
    }

    private function getGoogleSocialUserData(array $data): array
    {
        $googleClient = new Google_Client(['client_id' => config('services.google.client_id')]);
        $payload = $googleClient->verifyIdToken($data['tokenId']);

        return [
            'id' => $payload['sub'] ?? null,
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? null,
            'avatar' => $payload['picture'] ?? null,
            'raw' => $payload,
        ];
    }

    /**
     * @param BaseUserProfileRequest $request
     * @param User|Authenticatable $user
     * @return User
     * @throws ModelUpdateErrorException
     */
    public function update(BaseUserProfileRequest $request, User|Authenticatable $user): User
    {
        $data = (new AuthUpdateTransformer())->transform($request);
        $user->updated_by = $user->getKey();


        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['theme_mode'])) {
            $user->theme_mode = $data['theme_mode'];
        }

        if (isset($data['user_signature'])) {
            $user->user_signature = $data['user_signature'];
        }

        if (array_key_exists('profile', $data) && is_null($data['profile']) && $user->userDataFile) {
            StorageHelper::removeTusFile($user->userDataFile);
        }

        if ($this->repository->update($user, $data)) {
            $this->logService->updateLog($user, $user);

            return $user;
        }

        throw new ModelUpdateErrorException();
    }

    public function showUserResource(User|Authenticatable $user): AuthResource
    {
        return new AuthResource($user);
    }
}
