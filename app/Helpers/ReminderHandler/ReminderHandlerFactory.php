<?php

namespace App\Helpers\ReminderHandler;

use App\Exceptions\CustomErrorException;
use App\Models\Reminder;

class ReminderHandlerFactory
{
    /**
     * @param Reminder $reminder
     * @return ReminderHandlerInterface
     * @throws CustomErrorException
     */
    public static function getReminderHandler(Reminder $reminder): ReminderHandlerInterface
    {
        return match ($reminder->related_entity) {
            Reminder::REMIND_ENTITY_ME => new MeReminderHandler($reminder),
            Reminder::RELATED_ENTITY_INVOICE => new InvoiceReminderHandler($reminder),
            Reminder::RELATED_ENTITY_SUBSCRIPTION => new SubscriptionReminderHandler($reminder),
            default => throw new CustomErrorException('Reminder type unknown'),
        };
    }
}
