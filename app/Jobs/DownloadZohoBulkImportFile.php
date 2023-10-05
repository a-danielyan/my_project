<?php

namespace App\Jobs;

use App\Events\ZohoAPIFail;
use App\Helpers\StorageHelper;
use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use App\Models\ZohoBulkImportJobs;
use com\zoho\crm\api\bulkread\APIException;
use com\zoho\crm\api\bulkread\BulkReadOperations;
use com\zoho\crm\api\bulkread\FileBodyWrapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class DownloadZohoBulkImportFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var ZohoService $zohoService */
        $zohoService = resolve(ZohoService::class);
        $response = null;

        /** @var ZohoBulkImportJobs $job */
        $job = ZohoBulkImportJobs::query()->where('job_id', $this->jobId)->first();
        if (!$job) {
            Log::error('Job with id ' . $this->jobId . ' not founded');

            return;
        }

        try {
            /** @var OauthToken $token */
            $token = OauthToken::query()->where('service', 'zohocrm')->first();

            $zohoService->initializeZoho($token->grant_token);

            $bulkReadOperations = new BulkReadOperations();

            $response = $bulkReadOperations->downloadResult($job->job_id);
        } catch (Throwable $e) {
            ZohoAPIFail::dispatch('DownloadZohoBulkImportFile init ' . $e->getMessage());
        }

        if ($response != null) {
            echo('Status code ' . $response->getStatusCode() . "\n");
            if (in_array($response->getStatusCode(), array(204, 304))) {
                echo($response->getStatusCode() == 204 ? "No Content\n" : "Not Modified\n");

                return;
            }

            $responseHandler = $response->getObject();

            if ($responseHandler instanceof FileBodyWrapper) {
                $fileBodyWrapper = $responseHandler;

                $streamWrapper = $fileBodyWrapper->getFile();
                $stream = $streamWrapper->getStream();
                $storedFileName = 'zoho_bulk_import/' . $streamWrapper->getName();
                $fileData = StorageHelper::saveFile(
                    $stream,
                    $storedFileName,
                    $streamWrapper->getName(),
                    'zip',
                    ZohoBulkImportJobs::class,
                    $job->getKey(),
                );
                $fileData->save();

                $job->status = ZohoBulkImportJobs::STATUS_DOWNLOADED;
                $job->filename = $storedFileName;
                $job->save();
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

                    $job->status = ZohoBulkImportJobs::STATUS_FAILED;
                    $job->error = $exception->getMessage()->getValue();
                    $job->save();
                }
            }
        }
    }
}
