<?php

namespace App\Http\RequestTransformers\Account;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class AccountTrainingStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'trainingDate' => 'training_date',
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
