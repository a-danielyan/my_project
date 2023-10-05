<?php

namespace App\Console\Commands;

use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\modules\GetModulesHeader;
use com\zoho\crm\api\modules\ModulesOperations;
use com\zoho\crm\api\modules\ResponseWrapper;
use DateTimeZone;
use Illuminate\Console\Command;

class GetZohoModulesList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-zoho-modules-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get available zoho module list. This is one time task. needed only once';


    /**
     * @return void
     * @throws SDKException
     */
    public function handle(): void
    {
        $zohoService = resolve(ZohoService::class);
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $zohoService->initializeZoho($token->grant_token);


//Get instance of ModulesOperations Class
        $moduleOperations = new ModulesOperations();

        $headerInstance = new HeaderMap();

        $datetime = date_create("2020-07-15T17:58:47+05:30")->setTimezone(
            new DateTimeZone(date_default_timezone_get()),
        );

        $headerInstance->add(GetModulesHeader::IfModifiedSince(), $datetime);

        //Call getModules method that takes headerInstance as parameters
        $response = $moduleOperations->getModules($headerInstance);

        if ($response != null) {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            if (in_array($response->getStatusCode(), array(204, 304))) {
                echo($response->getStatusCode() == 204 ? "No Content\n" : "Not Modified\n");

                return;
            }

            //Get object from response
            $responseHandler = $response->getObject();

            if ($responseHandler instanceof ResponseWrapper) {
                //Get the received ResponseWrapper instance
                $responseWrapper = $responseHandler;

                //Get the list of obtained Module instances
                $modules = $responseWrapper->getModules();

                foreach ($modules as $module) {
                    $moduleName = $module->getName();
                }
            }
        }
    }
}
