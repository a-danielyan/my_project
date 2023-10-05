<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\DeviceSyncRequest;
use App\Http\Services\DeviceService;
use Illuminate\Http\JsonResponse;

class DeviceController
{
    public function __construct(private DeviceService $deviceService)
    {
    }

    public function syncDevice(DeviceSyncRequest $request): JsonResponse
    {
        $this->deviceService->syncDevice($request->all());

        return response()->json();
    }
}
