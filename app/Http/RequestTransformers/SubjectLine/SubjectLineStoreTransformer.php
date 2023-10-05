<?php

namespace App\Http\RequestTransformers\SubjectLine;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SubjectLineStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'subjectText' => 'subject_text',
            ];
    }
}
