<?php

namespace App\Http\Controllers;

use App\Console\Commands\ConvertImportedZohoEntity;
use App\Console\Commands\CreateZohoBulkImport;
use App\Console\Commands\GetUsersFromZohoCrm;
use App\Console\Commands\ImportZohoBulkImportData;
use App\Exceptions\CustomErrorException;
use App\Http\Services\ZohoService;
use App\Jobs\DownloadZohoBulkImportFile;
use com\zoho\crm\api\exception\SDKException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use ReflectionException;

class ZohoController
{
    public function __construct(private ZohoService $zohoService)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws SDKException
     * @throws ReflectionException
     */
    public function authorization(Request $request): JsonResponse
    {
        $this->zohoService->generateAccessToken($request->all());

        return response()->json();
    }

    public function notification(Request $request): JsonResponse
    {
        $this->zohoService->handleZohoNotification($request);

        return response()->json();
    }

    public function oauthLink(): JsonResponse
    {
        return response()->json(['link' => $this->zohoService->getAuthorizeLink()]);
    }

    public function runImportJob(): JsonResponse
    {
        Artisan::call(CreateZohoBulkImport::class);

        return response()->json();
    }

    public function downloadImportJob(Request $request): JsonResponse
    {
        DownloadZohoBulkImportFile::dispatch($request->jobId);

        return response()->json();
    }

    public function importDownloadedZoho(): JsonResponse
    {
        Artisan::call(ImportZohoBulkImportData::class);

        return response()->json();
    }

    public function convertImportedZoho(): JsonResponse
    {
        Artisan::call(ConvertImportedZohoEntity::class);

        return response()->json();
    }

    public function runUserImport(): JsonResponse
    {
        Artisan::call(GetUsersFromZohoCrm::class);

        return response()->json();
    }

    /**
     * @return JsonResponse
     * @throws SDKException
     */
    public function enableNotification(): JsonResponse
    {
        $this->zohoService->enableNotification();

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws SDKException
     */
    public function getNotification(Request $request): JsonResponse
    {
        return response()->json($this->zohoService->getNotification($request->get('channelId')));
    }
}
