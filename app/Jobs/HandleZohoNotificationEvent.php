<?php

namespace App\Jobs;

use App\Events\ZohoAPIFail;
use App\Http\Services\ZohoNotificationHandler\ZohoNotificationHandlerFactory;
use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use App\Models\ZohoNotificationEvents;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\APIException;
use com\zoho\crm\api\record\GetRecordsParam;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\record\ResponseWrapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class HandleZohoNotificationEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $notificationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var ZohoService $zohoService */
        $zohoService = resolve(ZohoService::class);

        /** @var ZohoNotificationEvents $notificationEvent */
        $notificationEvent = ZohoNotificationEvents::query()->find($this->notificationId);
        if (!$notificationEvent) {
            Log::error('Notification with id ' . $this->notificationId . ' not founded');

            return;
        }
        try {
            /** @var OauthToken $token */
            $token = OauthToken::query()->where('service', 'zohocrm')->first();

            $zohoService->initializeZoho($token->grant_token);

            $moduleAPIName = $notificationEvent->module;
            $notificationData = $notificationEvent->notification_data;
            $notificationHandler = ZohoNotificationHandlerFactory::getNotificationHandlerForEntity($moduleAPIName);

            switch ($notificationData['operation']) {
                case 'update':
                case 'insert':
                    //Get instance of RecordOperations Class that takes moduleAPIName as parameter
                    $recordOperations = new RecordOperations();
                    $paramInstance = new ParameterMap();

                    $idList = $notificationData['ids'];
                    $fieldsList = [];
                    foreach ($notificationData['affected_fields'] as $values) {
                        foreach ($values as $valuesToUpdate) {
                            $fieldsList = array_merge($fieldsList, $valuesToUpdate);
                        }
                    }
                    $fieldsList = array_unique($fieldsList);

                    if (!empty($fieldsList)) {
                        $paramInstance->add(GetRecordsParam::fields(), implode(',', $fieldsList));
                    }
                    $paramInstance->add(GetRecordsParam::page(), 1);
                    $paramInstance->add(GetRecordsParam::perPage(), count($idList));
                    $paramInstance->add(GetRecordsParam::ids(), implode(',', $idList));
                    $headerInstance = new HeaderMap();
                    $response = $recordOperations->getRecords($moduleAPIName, $paramInstance, $headerInstance);

                    break;

                default:
                    Log::error(
                        'Unknown notification type ' . $notificationData['operation'] . ' for event '
                        . $this->notificationId,
                    );

                    return;
            }
        } catch (Throwable $e) {
            ZohoAPIFail::dispatch('HandleZohoNotificationEvent init ' . $e->getMessage());

            return;
        }

        if ($response != null) {
            echo('Status code ' . $response->getStatusCode() . "\n");

            $responseHandler = $response->getObject();

            if ($responseHandler instanceof ResponseWrapper) {
                $responseData = $responseHandler->getData();


                try {
                    foreach ($responseData as $record) {
                        switch ($notificationData['operation']) {
                            case 'update':
                                $notificationHandler->updateEntity($record->getId(), $record->getKeyValues());
                                break;

                            case 'insert':
                                $notificationHandler->createEntity($record->getKeyValues());
                                break;

                            default:
                                Log::error(
                                    'Unhandled notification type ' . $notificationData['operation']
                                    . ' for event ' . $this->notificationId,
                                );
                                break;
                        }
                    }

                    $notificationEvent->processing_status = 'DONE';
                    $notificationEvent->save();
                } catch (ModelNotFoundException $e) {
                    $notificationEvent->processing_status = 'ERROR';
                    $notificationEvent->error_message = $e->getMessage();
                    $notificationEvent->save();
                }
            } else {
                if ($responseHandler instanceof APIException) {
                    $exception = $responseHandler;

                    $errorMessage = 'Status: ' . $exception->getStatus()->getValue() . "\n";
                    $errorMessage .= 'Code: ' . $exception->getCode()->getValue() . "\n";
                    $errorMessage .= 'Details: ';
                    foreach ($exception->getDetails() as $key => $value) {
                        $errorMessage .= $key . " : " . $value . "\n";
                    }
                    $errorMessage .= 'Message: ' . $exception->getMessage()->getValue() . "\n";
                    ZohoAPIFail::dispatch('DownloadZohoBulkImportFile get file' . $errorMessage);

                    $notificationEvent->processing_status = 'ERROR';
                    $notificationEvent->error_message = $exception->getMessage()->getValue();
                    $notificationEvent->save();
                }
            }
        }
    }
}
