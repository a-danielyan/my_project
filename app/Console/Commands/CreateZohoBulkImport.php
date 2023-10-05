<?php

namespace App\Console\Commands;

use App\Events\ZohoAPIFail;
use App\Exceptions\CustomErrorException;
use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use App\Models\ZohoBulkImportJobs;
use com\zoho\crm\api\bulkread\ActionWrapper;
use com\zoho\crm\api\bulkread\APIException;
use com\zoho\crm\api\bulkread\BulkReadOperations;
use com\zoho\crm\api\bulkread\CallBack;
use com\zoho\crm\api\bulkread\Query;
use com\zoho\crm\api\bulkread\RequestWrapper;
use com\zoho\crm\api\bulkread\SuccessResponse;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\modules\MinifiedModule;
use com\zoho\crm\api\util\APIResponse;
use com\zoho\crm\api\util\Choice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateZohoBulkImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-zoho-bulk-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create zoho bulk import job';

    public const AVAILABLE_MOULES = [
        'Leads',
        'Accounts',
        'Contacts',
        'Deals',
        'Tasks',
        'Calls',
        'Products',
        'Quotes',   // Quotes = Estimates
        'Sales_Orders',
    ];

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

        foreach (self::AVAILABLE_MOULES as $moduleAPIName) {
            try {
                $response = $this->makeBulkApiRequest($moduleAPIName);
            } catch (CustomErrorException) {
                continue;
            }

            $actionHandler = $response->getObject();

            $bulkImportJob = new ZohoBulkImportJobs();
            $bulkImportJob->module = $moduleAPIName;
            $bulkImportJob->job_id = 0;
            if ($actionHandler instanceof ActionWrapper) {
                $actionWrapper = $actionHandler;
                $actionResponses = $actionWrapper->getData();

                foreach ($actionResponses as $actionResponse) {
                    if ($actionResponse instanceof SuccessResponse) {
                        $successResponse = $actionResponse;
                        $bulkImportJob->status = ZohoBulkImportJobs::STATUS_NEW;
                        $bulkImportJob->job_id = $successResponse->getDetails()['id'];
                    } else {
                        if ($actionResponse instanceof APIException) {
                            $this->handleErrorResponse($actionResponse, $bulkImportJob);
                        }
                    }
                }
            } else {
                if ($actionHandler instanceof APIException) {
                    $this->handleErrorResponse($actionHandler, $bulkImportJob);
                }
            }
            $bulkImportJob->save();
        }
    }

    private function handleErrorResponse(APIException $actionResponse, ZohoBulkImportJobs $bulkImportJob): void
    {
        $errorMessage = "Status: " . $actionResponse->getStatus()->getValue() . "\n";
        $errorMessage .= "Code: " . $actionResponse->getCode()->getValue() . "\n";
        $errorMessage .= "Details: ";
        foreach ($actionResponse->getDetails() as $key => $value) {
            $errorMessage .= $key . " : " . $value . "\n";
        }
        $errorMessage .= "Message: " . $actionResponse->getMessage()->getValue() . "\n";
        ZohoAPIFail::dispatch('CreateZohoBulkImport action response' . $errorMessage);
        $bulkImportJob->status = ZohoBulkImportJobs::STATUS_FAILED;
        $bulkImportJob->error = $actionResponse->getMessage()->getValue();
    }


    /**
     * @param string $moduleAPIName
     * @return APIResponse
     * @throws CustomErrorException
     */
    private function makeBulkApiRequest(string $moduleAPIName): APIResponse
    {
        try {
            $bulkReadOperations = new BulkReadOperations();
            $requestWrapper = new RequestWrapper();
            $callback = new CallBack();
            $callback->setUrl(config('services.zohoCrm.notification'));
            $callback->setMethod(new Choice('post'));
            $requestWrapper->setCallback($callback);

            $query = new Query();

            $module = new MinifiedModule();
            $module->setAPIName($moduleAPIName);
            $query->setModule($module);

            $query->setPage(1);
            $requestWrapper->setQuery($query);

            return $bulkReadOperations->createBulkReadJob($requestWrapper);
        } catch (Throwable $e) {
            Log::error('CreateZohoBulkImport init error with module ' . $moduleAPIName . ' ' . $e->getMessage());
            ZohoAPIFail::dispatch('CreateZohoBulkImport init ' . $e->getMessage());
            throw new CustomErrorException('Zoho error', 422);
        }
    }
}
