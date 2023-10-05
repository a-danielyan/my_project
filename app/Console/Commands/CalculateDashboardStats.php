<?php

namespace App\Console\Commands;

use App\Http\Services\DashboardService;
use Illuminate\Console\Command;

class CalculateDashboardStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-dashboard-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate dashboard stats';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var DashboardService $dashboardService */
        $dashboardService = resolve(DashboardService::class);

        $dashboardService->calculateMarketingStats();
        $dashboardService->calculateSalesStats();
        $dashboardService->calculateAccountStats();
    }
}
