<?php

namespace App\Jobs;

use App\Helpers\CustomFieldValuesHelper;
use App\Mail\ReminderEmail;
use App\Models\Reminder;
use App\Models\ReminderLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessReminder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param Model $model It can be Invoice or Subscription now
     * @param Reminder $reminder
     * @param string $reminderDate
     */
    public function __construct(private Model $model, private Reminder $reminder, private string $reminderDate)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reminderEmail = new ReminderEmail($this->reminder);

        switch ($this->reminder->remind_entity) {
            case Reminder::REMIND_ENTITY_CONTACT:
                $contact = $this->model->contact;

                $customFields = CustomFieldValuesHelper::getCustomFieldValues($contact, ['email']);

                if (!isset($customFields['email'])) {
                    Log::error(
                        'Unknown email',
                        ['reminderId' => $this->reminder->getKey(), ' relatedEntity' => $this->model->getKey()],
                    );
                    ReminderLog::query()->create([
                        'reminder_id' => $this->reminder->getKey(),
                        'sent_entity' => $this->model::class,
                        'sent_entity_id' => $this->model->getKey(),
                        'reminder_date' => $this->reminderDate,
                        'status' => ReminderLog::STATUS_ERROR,
                        'error' => 'Unknown remind entity type',
                    ]);

                    return;
                }

                $userEmail = $customFields['email'];
                break;

            default:
                Log::error('Unknown remind entity type', ['reminderId' => $this->reminder->getKey()]);
                ReminderLog::query()->create([
                    'reminder_id' => $this->reminder->getKey(),
                    'sent_entity' => $this->model::class,
                    'sent_entity_id' => $this->model->getKey(),
                    'reminder_date' => $this->reminderDate,
                    'status' => ReminderLog::STATUS_ERROR,
                    'error' => 'Unknown remind entity type',
                ]);

                return;
        }


        $mail = Mail::to($userEmail);
        if (!empty($this->reminder->reminder_cc)) {
            $mail = $mail->cc($this->reminder->reminder_cc);
        }

        if (!empty($this->reminder->reminder_bcc)) {
            $mail = $mail->bcc($this->reminder->reminder_bcc);
        }

        $result = $mail->send($reminderEmail);
        if (!$result) {
            Log::error('Unknown remind entity type', ['reminderId' => $this->reminder->getKey()]);
            ReminderLog::query()->create([
                'reminder_id' => $this->reminder->getKey(),
                'sent_entity' => $this->model::class,
                'sent_entity_id' => $this->model->getKey(),
                'reminder_date' => $this->reminderDate,
                'status' => ReminderLog::STATUS_ERROR,
                'error' => 'Email not sent',
            ]);

            return;
        }

        ReminderLog::query()->create([
            'reminder_id' => $this->reminder->getKey(),
            'sent_entity' => $this->model::class,
            'sent_entity_id' => $this->model->getKey(),
            'reminder_date' => $this->reminderDate,
            'status' => ReminderLog::STATUS_DONE,
        ]);
    }
}
