<?php

namespace App\Http\RulesAttachments;

interface UserProfileStrategyInterface
{
    /**
     * @return array
     */
    public function getRules(): array;
}
