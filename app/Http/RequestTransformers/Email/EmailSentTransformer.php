<?php

namespace App\Http\RequestTransformers\Email;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class EmailSentTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return
            [
                'sendTo' => 'sendTo',
                'cc' => 'cc',
                'bcc' => 'bcc',
                'subject' => 'subject',
                'message' => 'message',
                'relatedToId' => 'relatedToId',
                'relatedToEntity' => 'relatedToEntity',
                'attachments' => 'attachments',
                'sendAt' => 'send_at',
            ];
    }
}
