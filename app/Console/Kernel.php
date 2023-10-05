<?php

namespace App\Console;

use App\Console\Commands\CalculateDashboardStats;
use App\Console\Commands\ClearZohoRawEventsTable;
use App\Console\Commands\ConvertImportedZohoEntity;
use App\Console\Commands\FixZohoBulkImportRelation;
use App\Console\Commands\HandleReminders;
use App\Console\Commands\HandleScheduledEmail;
use App\Console\Commands\SyncApolloRecords;
use App\Console\Commands\UpdateInvoiceStatus;
use App\Console\Commands\UpdateZohoExpirySubscription;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telescope:prune --hours=6')->everySixHours();
        $schedule->command(UpdateInvoiceStatus::class)->hourly();
        $schedule->command(FixZohoBulkImportRelation::class)->everyThirtyMinutes();
        $schedule->command(ConvertImportedZohoEntity::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(UpdateZohoExpirySubscription::class)->hourly();
        $schedule->command(ClearZohoRawEventsTable::class)->daily();
        $schedule->command(HandleScheduledEmail::class)->everyFiveMinutes();
        $schedule->command(CalculateDashboardStats::class)->hourly();
        //$schedule->command(SyncApolloRecords::class)->hourly(); @todo enable it later
        //$schedule->command(HandleReminders::class)->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
