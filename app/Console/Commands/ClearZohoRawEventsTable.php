<?php

namespace App\Console\Commands;

use App\Models\ZohoNotificationRawData;
use Illuminate\Console\Command;

class ClearZohoRawEventsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-zoho-raw-events-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear database zoho notification raw data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ZohoNotificationRawData::query()->where('created_at', '<', now()->subDays(14))->delete();
    }
}
