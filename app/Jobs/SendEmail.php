<?php

namespace App\Jobs;

use App\Http\Services\EmailService;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Email $emailRecord)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var EmailService $emailService */
        $emailService = resolve(EmailService::class);

        try {
            $emailService->sendEmail($this->emailRecord);
        } catch (Throwable $e) {
            $this->emailRecord->status = Email::STATUS_ERROR;
            $this->emailRecord->error = $e->getMessage();
            $this->emailRecord->save();
        }
    }
}
