<?php

namespace App\Jobs;

use App\Exceptions\CustomErrorException;
use App\Models\Email;
use App\Models\MailgunNotificationRawData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateEmailStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public MailgunNotificationRawData $notification)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->updateEmailStatus();
            $this->notification->processing_status = 'DONE';
            $this->notification->save();
        } catch (CustomErrorException $e) {
            $this->notification->processing_status = 'ERROR';
            $this->notification->error_message = $e->getMessage();
            $this->notification->save();
        }
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    private function updateEmailStatus(): void
    {
        $notificationData = $this->notification->raw_data;

        $emailStatus = '';
        switch ($notificationData['event']) {
            case 'clicked':
                $emailStatus = Email::STATUS_CLICKED;
                break;

            case 'delivered':
                $emailStatus = Email::STATUS_DELIVERED;
                break;

            case 'complained':
                $emailStatus = Email::STATUS_COMPLAINED;
                break;
            case 'opened':
                $emailStatus = Email::STATUS_OPENED;
                break;
            case 'failed':
                $emailStatus = Email::STATUS_FAILED;
                break;

            case 'unsubscribed':
                $emailStatus = Email::STATUS_UNSUBSCRIBED;
                break;

            default:
                Log::error(
                    'Unsupported event type ' . $notificationData['event'] . ' for mailgun event.',
                    ['eventId' => $this->notification->getKey()],
                );
                break;
        }

        if (empty($emailStatus)) {
            throw new CustomErrorException(
                'Unsupported event type ' . $notificationData['event'] . ' for mailgun event.'
            );
        }

        $messageId = $notificationData['message']['headers']['message-id'];

        /** @var Email $emailRecord */
        $emailRecord = Email::query()->where('email_id', $messageId)->first();

        if (!$emailRecord) {
            throw new CustomErrorException('Email record with id ' . $messageId . 'not found ');
        }

        $emailRecord->status = $emailStatus;
        $emailRecord->save();
    }
}
