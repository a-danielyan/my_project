<?php

namespace App\Helpers\ReminderHandler;

use App\Exceptions\CustomErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface ReminderHandlerInterface
{
    /**
     * @return Builder[]|Collection
     * @throws CustomErrorException
     */
    public function getEntityForWork(): Collection|array;

    public function getEntityClass(): string;

    public function getDueDate(): string;
}
