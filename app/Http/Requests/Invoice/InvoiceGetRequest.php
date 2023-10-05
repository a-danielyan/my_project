<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\BaseGetFormRequest;

class InvoiceGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'status',
        'paymentTerm',
        'orderType',
        'shipCarrier',
    ];
}
