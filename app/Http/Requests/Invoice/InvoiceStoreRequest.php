<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Invoice;
use App\Models\Opportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceStoreRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return array_merge(
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
                'estimateId' => [
                    'nullable',
                    'exists:estimate,id',
                    'required_if:orderType,' . Opportunity::NEW_BUSINESS,
                ],
                'opportunityId' => [
                    'nullable',
                    'exists:opportunity,id',
                    'required_if:orderType,' . Opportunity::NEW_BUSINESS,
                ],
                'contactId' => [
                    'int',
                    'exists:contact,id',
                ],
                'clientPO' => [
                    'string',
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
                    Rule::in([Opportunity::EXISTED_BUSINESS, Opportunity::NEW_BUSINESS]),
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
                'itemGroups' => [
                    'required',
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
                'customFields' => [
                    'array',
                ],
                'discountPercent' => [
                    'nullable',
                    'decimal:0,2',
                ],
            ],
            $this->customFieldValidation(Invoice::class),
        );
    }

    private function checkAddressRequired(): string
    {
        if (isset($this['customFields']['ship-to-multiple']) && $this['customFields']['ship-to-multiple']) {
            return 'required';
        }

        return 'nullable';
    }
}
