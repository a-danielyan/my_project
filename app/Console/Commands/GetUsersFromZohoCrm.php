<?php

namespace App\Console\Commands;

use App\Http\Repositories\UserRepository;
use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use App\Models\Role;
use App\Models\User;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\users\GetUsersParam;
use com\zoho\crm\api\users\ResponseWrapper;
use com\zoho\crm\api\users\UsersOperations;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetUsersFromZohoCrm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-users-from-zoho-crm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @return void
     * @throws SDKException
     */
    public function handle(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = resolve(UserRepository::class);
        $standardRole = Role::query()->where('name', Role::STANDARD_USER_ROLE)->first();

        $zohoService = resolve(ZohoService::class);
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $zohoService->initializeZoho($token->grant_token);

        $page = 1;
        do {
            $usersOperations = new UsersOperations();
            $paramInstance = new ParameterMap();

            $paramInstance->add(GetUsersParam::page(), $page);
            $paramInstance->add(GetUsersParam::perPage(), 20);

            $response = $usersOperations->getUsers($paramInstance);

            $responseHandler = $response->getObject();

            if ($responseHandler instanceof ResponseWrapper) {
                //Get the received ResponseWrapper instance
                $responseWrapper = $responseHandler;

                //Get the list of obtained User instances
                $users = $responseWrapper->getUsers();

                foreach ($users as $user) {
                    try {
                        $existedEmail = $userRepository->first(where: ['email' => $user->getEmail()]);
                        if ($existedEmail) {
                            $userRepository->update($existedEmail, ['zoho_entity_id' => $user->getId()]);
                            continue;
                        }

                        $userRepository->firstOrCreate(['zoho_entity_id' => $user->getId()], [
                            'first_name' => $user->getFirstName() ?? '',
                            'last_name' => $user->getLastName() ?? '',
                            'avatar' => '',
                            'email' => $user->getEmail(),
                            'status' => User::STATUS_INACTIVE,
                            'role_id' => $standardRole->getKey(),
                            'zoho_entity_id' => $user->getId(),
                        ]);
                    } catch (Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $page++;
        } while (!empty($responseHandler));
    }
}
