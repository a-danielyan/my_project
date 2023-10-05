<?php

namespace App\Http\Controllers;

use App\Http\Requests\Config\CitySearchRequest;
use App\Http\Services\CommonCityService;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    private CommonCityService $cityService;

    /**
     * ConfigController constructor.
     * @param CommonCityService $cityService
     */
    public function __construct(
        CommonCityService $cityService,
    ) {
        $this->cityService = $cityService;
    }

    /**
     * @param CitySearchRequest $request
     * @return JsonResponse
     */
    public function findLocation(CitySearchRequest $request): JsonResponse
    {
        return response()->json($this->cityService->findLocation($request->input('search')));
    }
}
