<?php

namespace App\Http\RequestTransformers\Account;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class AccountDemoStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'demoDate' => 'demo_date',
                'dueDate' => 'due_date',
                'trainedBy' => 'trained_by',
                'note' => 'note',
                'subject' => 'subject',
                'description' => 'description',
                'priority' => 'priority',
                'relatedTo' => 'related_to',
            ];
    }
}
