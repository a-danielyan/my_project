<?php

namespace App\Helpers\ZohoImport;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ZohoAPILimitException;
use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Services\ZohoBooksService;
use App\Http\Services\ZohoService;
use App\Models\CustomField;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OauthToken;
use App\Models\Product;
use App\Models\User;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\GetRecordParam;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\record\ResponseWrapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Weble\ZohoClient\Exception\AccessDeniedException;
use Weble\ZohoClient\Exception\AccessTokenNotSet;
use Weble\ZohoClient\Exception\RefreshTokenNotSet;
use Webleit\ZohoBooksApi\ZohoBooks;

class SalesOrderZohoMapper extends BaseZohoMapper
{
    private ZohoBooks $zohoBooks;
    private ZohoBooksService $zohoBooksService;
    private array $invoiceLineItems = [];

    /**
     * @throws IdentityProviderException
     * @throws AccessDeniedException
     * @throws AccessTokenNotSet
     * @throws RefreshTokenNotSet
     */
    public function __construct()
    {
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $this->zohoBooksService = resolve(ZohoBooksService::class);
        $zohoBooksClient = $this->zohoBooksService->initializeZoho($token);

        $this->zohoBooks = new ZohoBooks($zohoBooksClient);
        $zohoService = resolve(ZohoService::class);
        $zohoService->initializeZoho($token->grant_token);
    }


    public function getEntityClassName(): string
    {
        return Invoice::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(InvoiceRepository::class);
    }

    /**
     * @param array $zohoData
     * @param bool $isUpdate
     * @return array
     * @throws CustomErrorException
     */
    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $this->invoiceLineItems = [];
        $this->getLineItems($zohoData);

        preg_match('/invoices\/([0-9]+)/', $zohoData['Invoice_Link'], $invoiceResult);
        $zohoEntityIdInvoice = null;
        if (isset($invoiceResult[1])) {
            $zohoEntityIdInvoice = $invoiceResult[1];
        } else {
            if (empty($this->invoiceLineItems)) {
                throw new CustomErrorException('Invoice id not found');
            }
        }

        $cronUser = CommonHelper::getCronUser();

        $internalFields = [
            'created_by' => $cronUser->getKey(),
            'zoho_entity_id_sales_order' => $zohoData['Id'],
            'zoho_entity_id_invoice' => $zohoEntityIdInvoice,
            'opportunity_id' => $this->getRelatedOpportunityId(
                $this->getZohoMapperValueAsString($zohoData['Deal_Name']),
            ),
            'estimate_id' => $this->getRelatedEstimateId($this->getZohoMapperValueAsString($zohoData['Quote_Name'])),
            'account_id' => $this->getRelatedAccountId($this->getZohoMapperValueAsString($zohoData['Account_Name'])),
            'contact_id' => $this->getRelatedContactId($this->getZohoMapperValueAsString($zohoData['Contact_Name'])),
            'sub_total' => round((float)$zohoData['Sub_Total'], 2),
            'total_tax' => round((float)$zohoData['Tax'], 2),
            'total_discount' => round((float)$zohoData['Discount'], 2),
            'grand_total' => round((float)$zohoData['Grand_Total'], 2),
            'payment_term' => $this->getZohoMapperValueAsString($zohoData['Payment_Terms']),
            'due_date' => !empty($zohoData['Due_Date']) ? $zohoData['Due_Date'] : null,
            'terms_and_conditions' => !empty($zohoData['Terms_and_Conditions']) ? $zohoData['Terms_and_Conditions']
                : null,
            'status' => $this->getZohoMapperValueAsString($zohoData['Status']),
            'client_po' => !empty($zohoData['Customer_PO']) ? $zohoData['Customer_PO'] : null,
            'notes' => !empty($zohoData['Notes']) ? $zohoData['Notes'] : null,
            'owner_id' => $this->getRelatedUserId($this->getZohoMapperValueAsString($zohoData['Owner'])),
            'order_type' => $this->getZohoMapperValueAsString($zohoData['Order_Type']),
            'ship_date' => !empty($zohoData['Ship_Date_Date']) ? $zohoData['Ship_Date_Date'] : null,
            'ship_instruction' => !empty($zohoData['Shipping_Instructions']) ? $zohoData['Shipping_Instructions']
                : null,
            'track_code_standard' => !empty($zohoData['Tracking_Code']) ? $zohoData['Tracking_Code'] : null,
            'track_code_special' => !empty($zohoData['Tracking_Code2']) ? $zohoData['Tracking_Code2'] : null,
            'ship_cost' => !empty($zohoData['Shipping_Charge']) ? $zohoData['Shipping_Charge'] : null,
            'cancel_reason' => !empty($zohoData['Cancel_Void_Reason']) ? $zohoData['Cancel_Void_Reason'] : null,
            'cancel_details' => !empty($zohoData['Void_Cancel_Reason']) ? $zohoData['Void_Cancel_Reason'] : null,
            'refund_date' => !empty($zohoData['Refund_Date']) ? $zohoData['Refund_Date'] : null,
            'refund_reason' => !empty($zohoData['Reason_for_Refund']) ? $zohoData['Reason_for_Refund'] : null,
            'balance_due' => round((float)$zohoData['Balance'], 2),
        ];

        if ($internalFields['status'] === 'Approved for Fulfillment') {
            $internalFields['status'] = 'Approved for Fullfilment';
        }

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }

    private function getLineItems(array $zohoData): void
    {
        $lineItems = [];
        $recordId = $zohoData['Id'];
        $moduleAPIName = 'Sales_Orders';
        $recordOperations = new RecordOperations();
        $paramInstance = new ParameterMap();
        $headerInstance = new HeaderMap();

        $fieldNames = array("Ordered_Items");
        foreach ($fieldNames as $fieldName) {
            $paramInstance->add(GetRecordParam::fields(), $fieldName);
        }

        $response = $recordOperations->getRecord($recordId, $moduleAPIName, $paramInstance, $headerInstance);
        if ($response != null) {
            if (in_array($response->getStatusCode(), array(204, 304))) {
                return;
            }
            if ($response->isExpected()) {
                $responseHandler = $response->getObject();
                if ($responseHandler instanceof ResponseWrapper) {
                    $responseWrapper = $responseHandler;
                    $records = $responseWrapper->getData();
                    foreach ($records as $record) {
                        foreach ($record->getKeyValues() as $keyName => $value) {
                            if ($keyName == 'Ordered_Items') {
                                $allItems = $value;

                                foreach ($allItems as $item) {
                                    $productSku = '';
                                    $productQty = 0;
                                    $productPrice = 0;
                                    $productDiscount = 0;
                                    $productTotal = 0;
                                    $productTax = 0;
                                    foreach ($item->getKeyValues() as $lineItemKey => $lineItemValue) {
                                        if ($lineItemKey == 'Product_Name') {
                                            foreach ($lineItemValue->getKeyValues() as $productKey => $productValue) {
                                                if ($productKey == 'Product_Code') {
                                                    $productSku = $productValue;
                                                }
                                            }
                                        }

                                        if ($lineItemKey == 'Quantity') {
                                            $productQty = $lineItemValue;
                                        }
                                        if ($lineItemKey == 'List_Price') {
                                            $productPrice = $lineItemValue;
                                        }
                                        if ($lineItemKey == 'Discount') {
                                            $productDiscount = $lineItemValue;
                                        }
                                        if ($lineItemKey == 'Total') {
                                            $productTotal = $lineItemValue;
                                        }
                                        if ($lineItemKey == 'Tax') {
                                            $productTax = $lineItemValue;
                                        }
                                    }

                                    $lineItems[] = [
                                        'invoice_id' => null,
                                        'product_id' => $this->getProductId($productSku),
                                        'quantity' => $productQty,
                                        'price' => $productPrice,
                                        'discount' => $productDiscount,
                                        'total' => $productTotal,
                                        'subtotal' => $productTotal,
                                        'tax' => $productTax,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->invoiceLineItems = $lineItems;
    }


    /**
     * @param string $opportunityId
     * @return int
     * @throws CustomErrorException
     */
    private function getRelatedOpportunityId(string $opportunityId): int
    {
        return $this->getRelatedId('Deals', $opportunityId);
    }

    /**
     * @param string $estimateId
     * @return int
     * @throws CustomErrorException
     */
    private function getRelatedEstimateId(string $estimateId): int
    {
        return $this->getRelatedId('Quotes', $estimateId);
    }

    /**
     * @param string $accountId
     * @return int
     * @throws CustomErrorException
     */
    private function getRelatedAccountId(string $accountId): int
    {
        return $this->getRelatedId('Accounts', $accountId);
    }

    /**
     * @param string $contactId
     * @return int
     * @throws CustomErrorException
     */
    private function getRelatedContactId(string $contactId): int
    {
        return $this->getRelatedId('Contacts', $contactId);
    }


    private function getInvoiceLineItemsData(Invoice $invoice): array
    {
        $lineItems = [];
        if (!empty($this->invoiceLineItems)) {
            foreach ($this->invoiceLineItems as $lineItem) {
                $lineItem['invoice_id'] = $invoice->getKey();
                $lineItems[] = $lineItem;
            }

            return $lineItems;
        }

        try {
            $this->zohoBooksService->validateRequestUsages();
        } catch (ZohoAPILimitException) {
            echo 'Zoho API limit reached';
            exit();
        }
        $singleInvoice = $this->zohoBooks->invoices->get($invoice->zoho_entity_id_invoice);

        $invoiceData = $singleInvoice->getData();


        foreach ($invoiceData['line_items'] as $lineItem) {
            $lineItems[] = [
                'invoice_id' => $invoice->getKey(),
                'product_id' => $this->getProductId($lineItem['sku']),
                'quantity' => $lineItem['quantity'],
                'price' => $lineItem['rate'],
                'discount' => $lineItem['discount_amount'],
                'total' => $lineItem['item_total'],
                'subtotal' => $lineItem['item_total'],
                'tax' => $this->calculateTaxAmount($lineItem['line_item_taxes']),
            ];
        }

        return $lineItems;
    }


    private function getProductId(string $productSKU)
    {
        return Cache::remember('ZohoProductSKU#' . $productSKU, 86400, function () use ($productSKU) {
            $productCodeCustomField = CustomField::query()->where('entity_type', Product::class)
                ->where('code', 'product-code')->first();

            /** @var Product $product */
            $product = Product::query()->where('status', User::STATUS_ACTIVE)->whereHas(
                'customFields',
                function ($query) use ($productCodeCustomField, $productSKU) {
                    $query->where('entity', Product::class)
                        ->where('field_id', $productCodeCustomField->getKey())
                        ->where('text_value', $productSKU);
                },
            )->first();

            if ($product) {
                return $product->getKey();
            }

            throw new CustomErrorException('Related product with sku ' . $productSKU . ' not found');
        });
    }


    private function calculateTaxAmount(array $lineItemTaxes)
    {
        $taxAmount = 0;

        foreach ($lineItemTaxes as $taxes) {
            $taxAmount += $taxes['tax_amount'];
        }

        return $taxAmount;
    }


    public function afterInserted(Model $model, array $zohoData): void
    {
        /** @var Invoice $model */
        $invoiceLineItems = $this->getInvoiceLineItemsData($model);

        InvoiceItem::query()->where('invoice_id', $model->getKey())->delete();
        foreach ($invoiceLineItems as $lineItem) {
            InvoiceItem::query()->create($lineItem);
        }
    }
}
