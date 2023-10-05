<?php

namespace App\Http\Services;

use App\Console\Commands\CreateZohoBulkImport;
use App\Exceptions\CustomErrorException;
use App\Http\Repositories\ZohoTokenStoreRepository;
use App\Jobs\DownloadZohoBulkImportFile;
use App\Jobs\HandleZohoNotificationEvent;
use App\Models\ZohoBulkImportJobs;
use App\Models\ZohoNotificationEvents;
use App\Models\ZohoNotificationRawData;
use App\Models\ZohoNotificationSubscription;
use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\api\authenticator\OAuthToken;
use com\zoho\api\logger\LogBuilder;
use com\zoho\crm\api\dc\USDataCenter;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\InitializeBuilder;
use com\zoho\crm\api\notifications\ActionWrapper;
use com\zoho\crm\api\notifications\APIException;
use com\zoho\crm\api\notifications\BodyWrapper;
use com\zoho\crm\api\notifications\GetNotificationsParam;
use com\zoho\crm\api\notifications\Notification;
use com\zoho\crm\api\notifications\NotificationsOperations;
use com\zoho\crm\api\notifications\ResponseWrapper;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\SDKConfigBuilder;
use com\zoho\crm\api\util\Choice;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionException;
use Throwable;

class ZohoService
{
    /**
     * @param array $data
     * @return void
     * @throws CustomErrorException
     * @throws SDKException
     * @throws ReflectionException
     */
    public function generateAccessToken(array $data): void
    {
        Log::debug('Get response from zoho', $data);
        if (isset($data['error'])) {
            throw new CustomErrorException($data['error'], 422);
        }
        $token = $this->initializeZoho($data['code']);
        $token->getToken();
    }

    public function getAuthorizeLink(): string
    {
        $availableScopes = [
            'ZohoCRM.modules.READ',
            'ZohoCRM.bulk.READ',
            'ZohoCRM.notifications.ALL',
            'ZohoCRM.modules.Leads.READ',
            'ZohoCRM.modules.Accounts.READ',
            'ZohoCRM.modules.Contacts.READ',
            'ZohoCRM.modules.Deals.READ',
            'ZohoCRM.modules.Tasks.READ',
            'ZohoCRM.settings.READ',
            'ZohoCRM.modules.Calls.READ',
            'ZohoCRM.modules.Products.READ',
            'ZohoCRM.modules.Quotes.READ',
            'ZohoCRM.modules.Sales_Orders.READ',

        ];

        return 'https://accounts.zoho.com/oauth/v2/auth?scope=' . implode(',', $availableScopes) . '&client_id=' .
            config('services.zohoCrm.client_id') . '&response_type=code&access_type=offline&redirect_uri=' . config(
                'services.zohoCrm.redirect',
            );
    }

    /**
     * @param string $grantToken
     * @return OAuthToken
     * @throws SDKException
     */
    public function initializeZoho(string $grantToken): OAuthToken
    {
        $store = new ZohoTokenStoreRepository();

        $token = (new OAuthBuilder())
            ->clientId(config('services.zohoCrm.client_id'))
            ->clientSecret(config('services.zohoCrm.client_secret'))
            ->grantToken($grantToken)
            //->redirectURL(config('services.zohoCrm.redirect'))
            ->build();

        $logger = (new LogBuilder())
            ->level('OFF')
            // ->filePath(storage_path("php_sdk_log.log"))
            ->build();

        $sdkConfig = (new SDKConfigBuilder())
            ->autoRefreshFields(false)
            ->pickListValidation(false)
            ->sslVerification(false)
            ->connectionTimeout(10)
            ->timeout(10)
            ->build();

        $environment = match (config('app.env')) {
            'production', 'local' => USDataCenter::PRODUCTION(),
            default => USDataCenter::SANDBOX(),
        };

        (new InitializeBuilder())
            ->environment($environment)
            ->token($token)
            ->store($store)
            ->SDKConfig($sdkConfig)
            ->logger($logger)
            ->initialize();

        return $token;
    }

    public function handleZohoNotification(Request $request): void
    {
        ZohoNotificationRawData::query()->create(['raw_data' => json_encode($request->all())]);
        if (isset($request->channel_id)) {
            try {
                if ($request->get('token') === config('services.zohoCrm.zohoVerificationString')) {
                    $notificationData = $request->all();
                    unset($notificationData['module']);
                    unset($notificationData['resource_uri']);
                    unset($notificationData['channel_id']);
                    unset($notificationData['token']);

                    /** @var ZohoNotificationEvents $notificationEvent */
                    $notificationEvent = ZohoNotificationEvents::query()->create([
                        'channel_id' => $request->get('channel_id'),
                        'module' => $request->get('module'),
                        'notification_data' => $notificationData,
                    ]);
                    HandleZohoNotificationEvent::dispatch($notificationEvent->getKey());
                }
            } catch (Throwable $e) {
                Log::error($e->getMessage());
            }

            return;
        }


        if ($request->input('state') === 'COMPLETED') {
            $jobId = $request->input('job_id');
            DownloadZohoBulkImportFile::dispatch($jobId);
        } else {
            $jobId = $request->input('job_id');
            Log::error('Cant generate bulk import job', $request->all());
            /** @var ZohoBulkImportJobs $job */
            $job = ZohoBulkImportJobs::query()->where('job_id', $jobId)->first();
            if ($job) {
                $job->status = ZohoBulkImportJobs::STATUS_FAILED;
                $job->save();
            } else {
                Log::error('Job with id ' . $jobId . ' not founded');
            }
        }
    }

    /**
     * @return void
     * @throws SDKException
     */
    public function enableNotification(): void
    {
        /** @var \App\Models\OauthToken $token */
        $token = \App\Models\OauthToken::query()->where('service', 'zohocrm')->first();

        $this->initializeZoho($token->grant_token);

        foreach (CreateZohoBulkImport::AVAILABLE_MOULES as $module) {
            $existedNotification = ZohoNotificationSubscription::query()->where('module', $module)->first();

            if ($existedNotification) {
                continue;
            }

            $notificationChannelId = time();

            try {
                $channelExpiry = $this->enableNotificationForModule($module, $notificationChannelId);

                ZohoNotificationSubscription::query()->create([
                    'chanel_id' => $notificationChannelId,
                    'module' => $module,
                    'expired_at' => $channelExpiry->format('Y-m-d H:i:s'),
                ]);
            } catch (CustomErrorException) {
            }
        }
    }

    /**
     * @param string $module
     * @param int $notificationChannelId
     * @return DateTime
     * @throws CustomErrorException
     */
    public function enableNotificationForModule(string $module, int $notificationChannelId): DateTime
    {
        $events = [$module . ".all"];
        Log::info('Start create notification channel with Id =' . $notificationChannelId);

        $notificationOperations = new NotificationsOperations();
        $bodyWrapper = new BodyWrapper();

        $notification = new Notification();
        $notification->setChannelId((string)$notificationChannelId);

        $notification->setEvents($events);

        $notification->setToken(config('services.zohoCrm.zohoVerificationString'));
        $notification->setNotifyUrl(config('services.zohoCrm.notification'));
        $channelExpiry = new DateTime();
        $channelExpiry->modify('+23 hours');
        $notification->setChannelExpiry($channelExpiry);

        $notifications = [$notification];


        $bodyWrapper->setWatch($notifications);
        //Call enableNotifications method that takes BodyWrapper instance as parameter
        $response = $notificationOperations->enableNotifications($bodyWrapper);


        $actionHandler = $response->getObject();
        if ($actionHandler instanceof ActionWrapper) {
            $actionWrapper = $actionHandler;
            $actionResponses = $actionWrapper->getWatch();
            foreach ($actionResponses as $response) {
                if ($response->getCode()->getValue() == 'SUCCESS') {
                    return $channelExpiry;
                }
                throw new CustomErrorException($response->getCode()->getValue(), 422);
            }
        } else {
            $errorMessage = '';
            if ($actionHandler instanceof APIException) {
                $errorMessage = "Status: " . $actionHandler->getStatus()->getValue() . "\n";
                $errorMessage .= "Code: " . $actionHandler->getCode()->getValue() . "\n";
                $errorMessage .= "Details: ";
                foreach ($actionHandler->getDetails() as $key => $value) {
                    $errorMessage .= $key . " : " . $value . "\n";
                }
                $errorMessage .= "Message: " . $actionHandler->getMessage()->getValue() . "\n";

                echo $errorMessage;
            }
            throw new CustomErrorException($errorMessage, 422);
        }
        throw new CustomErrorException('Unknown error', 422);
    }


    /**
     * @param string $channelId
     * @return array
     * @throws SDKException
     */
    #[ArrayShape([
        'errorMessage' => 'string',
        'notifications' => 'array',
    ])]
    public function getNotification(string $channelId): array
    {
        /** @var \App\Models\OauthToken $token */
        $token = \App\Models\OauthToken::query()->where('service', 'zohocrm')->first();

        $this->initializeZoho($token->grant_token);


        $notificationOperations = new NotificationsOperations();
        $paramInstance = new ParameterMap();
        $paramInstance->add(GetNotificationsParam::channelId(), $channelId);
        $response = $notificationOperations->getNotifications($paramInstance);

        $errorMessage = '';
        $notifications = [];

        if ($response != null) {
            if (in_array($response->getStatusCode(), array(204, 304))) {
                return ['errorMessage' => $errorMessage, 'notifications' => $notifications];
            }
            if ($response->isExpected()) {
                $responseHandler = $response->getObject();
                if ($responseHandler instanceof ResponseWrapper) {
                    $responseWrapper = $responseHandler;
                    foreach ($responseWrapper->getWatch() as $notification) {
                        $notifications[] = [
                            'expiry' => $notification->getChannelExpiry(),
                            'notifyUrl' => $notification->getNotifyUrl(),
                            'events' => $notification->getEvents(),
                            'resource' => $notification->getResourceName(),
                        ];
                    }
                } else {
                    if ($responseHandler instanceof APIException) {
                        $exception = $responseHandler;

                        $errorMessage = "Status: " . $exception->getStatus()->getValue();
                        $errorMessage .= "Code: " . $exception->getCode()->getValue();
                        if ($exception->getDetails() != null) {
                            $errorMessage .= "Details: \n";
                            foreach ($exception->getDetails() as $keyName => $keyValue) {
                                $errorMessage .= $keyName . ": " . $keyValue . "\n";
                            }
                        }
                        $errorMessage .= "Message : " . (
                            $exception->getMessage() instanceof Choice ?
                                $exception->getMessage()->getValue() :
                                $exception->getMessage()
                            ) . "\n";
                    }
                }
            }
        }

        return ['errorMessage' => $errorMessage, 'notifications' => $notifications];
    }

    /**
     * @param int $channelId
     * @param string $moduleName
     * @param DateTime $channelExpiry
     * @return DateTime
     * @throws CustomErrorException
     */
    public function updateChannelExpiratoryNotification(
        int $channelId,
        string $moduleName,
        DateTime $channelExpiry,
    ): DateTime {
        $notificationOperations = new NotificationsOperations();
        $bodyWrapper = new BodyWrapper();
        $notification = new Notification();
        $notification->setChannelId((string)$channelId);
        $events = [$moduleName . ".all"];
        $notification->setEvents($events);
        $notification->setChannelExpiry($channelExpiry);
        $notification->setToken(config('services.zohoCrm.zohoVerificationString'));
        $notificationList = [$notification];
        $bodyWrapper->setWatch($notificationList);
        $response = $notificationOperations->updateNotification($bodyWrapper);


        $actionHandler = $response->getObject();
        if ($actionHandler instanceof ActionWrapper) {
            $actionWrapper = $actionHandler;
            $actionResponses = $actionWrapper->getWatch();
            foreach ($actionResponses as $response) {
                if ($response->getCode()->getValue() == 'SUCCESS') {
                    return $channelExpiry;
                }
            }
        } else {
            $errorMessage = '';
            if ($actionHandler instanceof APIException) {
                $errorMessage = "Status: " . $actionHandler->getStatus()->getValue() . "\n";
                $errorMessage .= "Code: " . $actionHandler->getCode()->getValue() . "\n";
                $errorMessage .= "Details: ";
                foreach ($actionHandler->getDetails() as $key => $value) {
                    $errorMessage .= $key . " : " . $value . "\n";
                }
                $errorMessage .= "Message: " . $actionHandler->getMessage()->getValue() . "\n";

                echo $errorMessage;
            }
            throw new CustomErrorException($errorMessage, 422);
        }
        throw new CustomErrorException('Unknown error', 422);
    }
}
