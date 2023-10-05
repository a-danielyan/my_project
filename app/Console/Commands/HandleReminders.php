<?php

namespace App\Console\Commands;

use App\Exceptions\CustomErrorException;
use App\Helpers\ReminderHandler\ReminderHandlerFactory;
use App\Jobs\ProcessReminder;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle all available reminders';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $remindersToProcess = Reminder::query()->where('status', User::STATUS_ACTIVE)->get();

        /** @var Reminder $reminder */
        foreach ($remindersToProcess as $reminder) {
            try {
                $reminderHandler = ReminderHandlerFactory::getReminderHandler($reminder);
            } catch (CustomErrorException) {
                Log::error(
                    'Unknown reminder entity ',
                    ['reminderId' => $reminder->getKey(), 'entity' => $reminder->related_entity],
                );
                continue;
            }

            try {
                $recordsToWork = $reminderHandler->getEntityForWork();
            } catch (CustomErrorException $e) {
                Log::error($e->getMessage(), ['reminderId' => $reminder->getKey()]);
                continue;
            }

            foreach ($recordsToWork as $recordForRemind) {
                if (
                    $reminder->reminderLogs->contains(function ($value) use ($recordForRemind, $reminderHandler) {
                        if (
                            $value->sent_entity_id == $recordForRemind->id &&
                            $value->reminder_date == $reminderHandler->getDueDate()
                        ) {
                            return true;
                        }

                        return false;
                    })
                ) {
                    ProcessReminder::dispatch($recordForRemind, $reminder, $reminderHandler->getDueDate());
                }
            }
        }
    }
}
