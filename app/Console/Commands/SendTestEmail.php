<?php

namespace App\Console\Commands;

use App\Mail\ZohoAPIError;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Mail::to('sramsiks@gmail.com')->send(
            new ZohoAPIError(
                'test message',
            ),
        );
    }
}
