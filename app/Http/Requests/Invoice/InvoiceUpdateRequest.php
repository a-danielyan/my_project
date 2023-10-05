<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'status' => [
                    'string',
                    Rule::in(Invoice::AVAILABLE_INVOICE_STATUSES),
                ],
                'paymentTerm' => [
                    'string',
                    Rule::in(Invoice::AVAILABLE_PAYMENT_TERMS),
                ],
                'dueDate' => [
                    'date',
                ],
                'termsAndConditions' => [
                    'string',
                ],
                'accountId' => [
                    'int',
                    'exists:account,id',
                ],
                'contactId' => [
                    'int',
                    'exists:contact,id',
                ],
                'clientPO' => [
                    'string',
                ],
                'opportunityId' => [
                    'int',
                    'exists:opportunity,id',
                ],
                'parentPO' => [
                    'string',
                ],
                'previousPO' => [
                    'string',
                ],
                'ownerId' => [
                    'int',
                    'exists:users,id',
                ],
                'orderType' => [
                    'string',
                ],
                'shipDate' => [
                    'date',
                ],
                'shipCarrier' => [
                    'string',
                ],
                'shipInstruction' => [
                    'string',
                ],
                'trackCodeStandard' => [
                    'string',
                ],
                'trackCodeSpecial' => [
                    'string',
                ],
                'shipCost' => [
                    'numeric',
                ],
                'cancelReason' => [
                    'string',
                ],
                'cancelDetails' => [
                    'string',
                ],
                'canceledBy' => [
                    'int',
                    'exists:users,id',
                ],
                'refundAmount' => [
                    'numeric',
                ],
                'refundDate' => [
                    'date',
                ],
                'refundReason' => [
                    'string',
                ],
                'refundedBy' => [
                    'int',
                    'exists:users,id',
                ],
                'customFields' => [
                    'array',
                ],
                'ignoreAddressChanges' => [
                    'nullable',
                    'boolean',
                ],
                'itemGroups' => [
                    'array',
                ],
                'itemGroups.*.contactId' => [
                    'nullable',
                    'exists:contact,id',
                ],
                'itemGroups.*.address' => [
                    $this->checkAddressRequired(),
                    'array',
                ],
                'itemGroups.*.items' => [
                    'required',
                    'array',
                ],
                'itemGroups.*.items.*.productId' => [
                    'required',
                    'exists:product,id',
                ],
                'itemGroups.*.items.*.quantity' => [
                    'required',
                    'numeric',
                ],
                'itemGroups.*.items.*.description' => [
                    'string',
                ],
                'itemGroups.*.items.*.combinePrice' => [
                    'boolean',
                    'nullable',
                ],
                'itemGroups.*.items.*.childItems' => [
                    'array',
                    'nullable',
                ],
                'itemGroups.*.items.*.tax_percent' => [
                    'decimal:0,2',
                    'nullable',
                ],
                'discountPercent' => [
                    'nullable',
                    'decimal:0,2',
                ],
            ];
    }

    private function checkAddressRequired(): string
    {
        if (isset($this['customFields']['ship-to-multiple']) && $this['customFields']['ship-to-multiple']) {
            return 'required';
        }

        return 'nullable';
    }
}
