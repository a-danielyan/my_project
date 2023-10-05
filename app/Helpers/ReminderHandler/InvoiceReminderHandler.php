<?php

namespace App\Helpers\ReminderHandler;

use App\Exceptions\CustomErrorException;
use App\Models\Invoice;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InvoiceReminderHandler implements ReminderHandlerInterface
{
    private Carbon $dueDate;

    public function __construct(private Reminder $reminder)
    {
    }

    /**
     * @return Builder[]|Collection
     * @throws CustomErrorException
     */
    public function getEntityForWork(): Collection|array
    {
        $recordsToWork = Invoice::query()->whereNotIn('status', [
            Invoice::INVOICE_STATUS_PAYMENT_COMPLETED,
            Invoice::INVOICE_STATUS_PARTIALLY_PAID,
        ]);


        $this->dueDate = match ($this->reminder->remind_type) {
            Reminder::REMIND_TYPE_AFTER => now()->subDays($this->reminder->remind_days),
            Reminder::REMIND_TYPE_BEFORE => now()->addDays($this->reminder->remind_days),
            default => throw new CustomErrorException('Unknown remind type'),
        };

        return $recordsToWork->where('due_date', $this->dueDate->format('Y-m-d'))->get();
    }

    public function getEntityClass(): string
    {
        return Invoice::class;
    }

    public function getDueDate(): string
    {
        return $this->dueDate->format('Y-m-d');
    }
}
