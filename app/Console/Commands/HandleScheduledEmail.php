<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use App\Models\Email;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class HandleScheduledEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-scheduled-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent scheduled emails';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Email::query()->where('status', Email::STATUS_SCHEDULED)
            ->where('send_at', '<', now())->chunk(10, function (Collection $items) {
                foreach ($items as $email) {
                    SendEmail::dispatch($email);
                }
            });
    }
}
