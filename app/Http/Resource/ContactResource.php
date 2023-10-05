<?php

namespace App\Http\Resource;

use App\Models\Contact;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;

/**
 * @mixin Contact
 */
class ContactResource extends ContactMinimalResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;
    use GetRecordStatusTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($this->resource == null) {
            return [];
        }

        return array_merge(parent::toArray($request), [
            'accountId' => new AccountMinimalResource($this->account, customFieldList: [
                'first-name',
                'last-name',
                'account-name',
            ]),
        ]);
    }
}
