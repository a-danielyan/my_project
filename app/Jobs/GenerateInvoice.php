<?php

namespace App\Jobs;

use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\InvoiceRepository;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceShippingGroupItem;
use App\Traits\TaxCalculationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use TaxCalculationTrait;

    /**
     * Create a new job instance.
     */
    public function __construct(public Estimate $estimate)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(InvoiceRepository $invoiceRepository): void
    {
        $opportunity = $this->estimate->opportunity;
        if (empty($opportunity)) {
            Log::error('For estimate ' . $this->estimate->id . ' we dont have opportunity');

            return;
        }
        $account = $this->estimate->account;
        if (empty($account)) {
            Log::error('For estimate ' . $this->estimate->id . ' we dont have account');

            return;
        }
        $contact = $this->estimate->contact;
        if (empty($contact)) {
            Log::error('For estimate ' . $this->estimate->id . ' we dont have contact');

            return;
        }

        $subTotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        $grandTotal = $subTotal + $totalTax - $totalDiscount;

        /** @var Invoice $invoice */
        $invoice = $invoiceRepository->create([
            'opportunity_id' => $opportunity->getKey(),
            'estimate_id' => $this->estimate->getKey(),
            'account_id' => $account->getKey(),
            'contact_id' => $contact->getKey(),
            'sub_total' => $subTotal,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
            'grand_total' => $grandTotal,
            'balance_due' => $grandTotal,
            'payment_term' => Invoice::PAYMENT_TERM_PREPAID,
            'due_date' => now(),
            'terms_and_conditions' => '',
        ]);

        $fileName = 'INV_' . date('Y') . '_' . $invoice->getKey();
        $invoice->filename = $fileName;
        $invoice->invoice_number = 'INV_' . date('Y') . $invoice->getKey();
        $invoice->save();

        $estimateCustomFields = CustomFieldValuesHelper::getCustomFieldValues($this->estimate, [
            'billing-street',
            'billing-city',
            'billing-state',
            'billing-code',
            'billing-country',
            'shipping-street',
            'shipping-city',
            'shipping-state',
            'shipping-postal-code',
            'shipping-country',
        ]);

        $invoiceCustomFields = CustomField::query()->where('entity_type', Invoice::class)->get();

        foreach ($invoiceCustomFields as $customField) {
            if (isset($estimateCustomFields[$customField->code])) {
                CustomFieldValues::query()->create(
                    [
                        'field_id' => $customField->getKey(),
                        'entity_id' => $invoice->getKey(),
                        'entity' => Invoice::class,
                        'text_value' => $estimateCustomFields[$customField->code],
                    ],
                );
            }
        }

        $customField = CustomFieldValues::query()->where('entity', Estimate::class)
            ->where('entity_id', $this->estimate->id)->whereHas('customField', function ($query) {
                $query->where('entity_type', Estimate::class)->where('code', 'estimate-owner');
            })->first();

        $invoice->owner_id = $customField?->integer_value ?? null;

        foreach ($this->estimate->estimateItemGroup as $itemGroup) {
            $invoiceItemGroup = InvoiceShippingGroupItem::query()->create([
                'contact_id' => $itemGroup->contact_id,
                'invoice_id' => $invoice->getKey(),
                'address' => $itemGroup->address,

            ]);

            foreach ($itemGroup->items as $item) {
                /** @var EstimateItem $item */
                $productPrice = CustomFieldValuesHelper::getProductPriceValue($item->product);

                $subTotal += $item->quantity * $productPrice;
                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->getKey(),
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'discount' => 0,
                    'total' => $productPrice * $item->quantity,
                    'subtotal' => $productPrice * $item->quantity,
                    'tax' => 0,
                    'group_id' => $invoiceItemGroup->getKey(),
                ]);
            }
        }

        /** @var CustomFieldValues $contactStateValue */
        $contactStateValue = CustomFieldValues::query()->where('entity', Contact::class)
            ->where('entity_id', $contact->getKey())->whereHas('customField', function ($query) {
                $query->where('entity_type', Contact::class)->where('code', 'contact-state');
            })->first();

        $contactState = '';
        if ($contactStateValue) {
            $contactState = $contactStateValue->text_value;
        }

        $totalTax = $this->calculateTax($subTotal, $contactState, $account->getKey());
        $grandTotal = $subTotal + $totalTax - $totalDiscount;
        $invoice->sub_total = $subTotal;
        $invoice->total_tax = $totalTax;
        $invoice->grand_total = $grandTotal;
        $invoice->save();
        GenerateStripeInvoiceWithSubscription::dispatch($invoice);
    }
}
