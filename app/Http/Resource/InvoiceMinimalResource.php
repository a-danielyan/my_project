<?php

namespace App\Http\Resource;

use App\Models\Invoice;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;

/**
 * @mixin Invoice
 */
class InvoiceMinimalResource extends BaseResourceWithCustomField
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $estimate = $this->estimate;
        $customFieldValues = $this->getCustomFieldValues();

        return [
            'id' => $this->id,
            'customFields' => $this->getCustomFields($customFieldValues),
            'invoiceNumber' => $this->invoice_number,
            'opportunityId' => $this->opportunity_id,
            'estimateId' => $estimate?->getKey(),
            'estimateName' => $estimate?->estimate_name,
            'createdAt' => $this->created_at,
            'paidAt' => $this->paid_at,
            'orderDate' => $this->order_date,
            'orderType' => $this->order_type,
            'itemGroups' => InvoiceItemGroupResource::collection($this->whenLoaded('invoiceItem')),
            'subTotal' => $this->sub_total,
            'totalTax' => $this->total_tax,
            'totalDiscount' => $this->total_discount,
            'grandTotal' => $this->grand_total,
            'payment_term' => $this->payment_term,
            'dueDate' => $this->due_date,
            'termsAndConditions' => $this->terms_and_conditions,
            'status' => $this->status,
            'statusLog' => InvoiceStatusLogResource::collection($this->whenLoaded('statusLog')),
            'filename' => $this->filename,
            'updatedBy' => $this->updated_by,
            //          'notes' => $this->notes,
            'invoiceOwner' => $this->invoiceOwner,
            'shipDate' => $this->ship_date,
            'shipCarrier' => $this->ship_carrier,
            'shipInstruction' => $this->ship_instruction,
            'trackCodeStandard' => $this->track_code_standard,
            'trackCodeSpecial' => $this->track_code_special,
            'shipCost' => $this->ship_cost,
            'SQLToOrderDuration' => $this->sql_to_order_duration,
            'cancelReason' => $this->cancel_reason,
            'cancelDetails' => $this->cancel_details,
            'canceledBy' => $this->canceledBy,
            'refundAmount' => $this->refund_amount,
            'refundDate' => $this->refund_date,
            'refundReason' => $this->refund_reason,
            'refundedBy' => $this->refundedBy,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'balanceDue' => $this->balance_due,
            'taxPercent' => $this->tax_percent,
            'discountPercent' => $this->discount_percent,
        ];
    }
}
