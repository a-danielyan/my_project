<?php

namespace App\Helpers\ReminderHandler;

use App\Exceptions\CustomErrorException;
use App\Models\Reminder;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionReminderHandler implements ReminderHandlerInterface
{
    public function __construct(private Reminder $reminder)
    {
    }

    public function getEntityForWork(): Collection|array
    {
        // TODO: Implement getEntityForWork() method.

        throw new CustomErrorException('Logic not implemented yet ');
    }

    public function getEntityClass(): string
    {
        // TODO: Implement getEntityClass() method.
        return Subscription::class;
    }

    public function getDueDate(): string
    {
        // TODO: Implement getDueDate() method.
        return '';
    }
}
