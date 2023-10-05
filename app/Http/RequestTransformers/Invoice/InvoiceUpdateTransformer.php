<?php

namespace App\Http\RequestTransformers\Invoice;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class InvoiceUpdateTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'status' => 'status',
                'paymentTerm' => 'payment_term',
                'dueDate' => 'due_date',
                'termsAndConditions' => 'terms_and_conditions',
                'accountId' => 'account_id',
                'contactId' => 'contact_id',
                'opportunityId' => 'opportunity_id',
                'clientPO' => 'client_po',
                'parentPO' => 'parent_po',
                'previousPO' => 'previous_po',
                'notes' => 'notes',
                'ownerId' => 'owner_id',
                'orderType' => 'order_type',
                'shipDate' => 'ship_date',
                'shipCarrier' => 'ship_carrier',
                'shipInstruction' => 'ship_instruction',
                'trackCodeStandard' => 'track_code_standard',
                'trackCodeSpecial' => 'track_code_special',
                'shipCost' => 'ship_cost',
                'cancelReason' => 'cancel_reason',
                'cancelDetails' => 'cancel_details',
                'canceledBy' => 'canceled_by',
                'refundAmount' => 'refund_amount',
                'refundDate' => 'refund_date',
                'refundReason' => 'refund_reason',
                'refundedBy' => 'refunded_by',
                'balanceDue' => 'balance_due',
                'customFields' => 'customFields',
                'ignoreAddressChanges' => 'ignoreAddressChanges',
                'invoiceItems' => 'invoiceItems',
            ];
    }
}
