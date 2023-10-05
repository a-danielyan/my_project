<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->service->getStats($this->getUser()));
    }
}
