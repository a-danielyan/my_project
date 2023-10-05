<?php

namespace App\Http\Services;

use App\Helpers\CustomFieldValuesHelper;
use App\Models\Invoice;
use App\Traits\FillShippingAndBillingAddressTrait;
use Illuminate\Support\Facades\Log;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\AnetApiResponseType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OpaqueDataType;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\SettingType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;

class AuthorizeNetService
{
    use FillShippingAndBillingAddressTrait;

    private MerchantAuthenticationType $merchantAuthentication;

    public function __construct()
    {
        $this->merchantAuthentication = new MerchantAuthenticationType();
        $this->merchantAuthentication->setName(config('services.authorizeNet.loginId'));
        $this->merchantAuthentication->setTransactionKey(config('services.authorizeNet.transactionKey'));
    }

    public function createAnAcceptPaymentTransaction(Invoice $invoice, array $data): ?AnetApiResponseType
    {
        // Create the payment object for a payment nonce with data received from FE
        $opaqueData = new OpaqueDataType();
        $opaqueData->setDataDescriptor($data['opaqueDataDescriptor']);
        $opaqueData->setDataValue($data['opaqueDataValue']);

        $paymentOne = new PaymentType();
        $paymentOne->setOpaqueData($opaqueData);


        $order = new OrderType();
        $order->setInvoiceNumber($invoice->invoice_number);
        //   $order->setDescription("Golf Shirts");


        $invoiceCustomFields = CustomFieldValuesHelper::getCustomFieldValues($invoice);

        $contact = $invoice->contact;

        $contactCustomField = CustomFieldValuesHelper::getCustomFieldValues($contact);

        $savedBillingAddress = [];
        $savedShippingAddress = [];

        $this->convertSavedAddressArray($contact, $savedBillingAddress, $savedShippingAddress);

        $account = $invoice->account;

        $accountCustomField = CustomFieldValuesHelper::getCustomFieldValues($account, ['account-name']);

        $billingAddress = '';
        $billingCity = '';
        $billingState = '';
        $billingZip = '';
        $billingCountry = '';


        if (isset($invoiceCustomFields['billing-street'])) {
            $billingAddress = $invoiceCustomFields['billing-street'];
        } elseif (isset($savedBillingAddress['address1'])) {
            $billingAddress = $savedBillingAddress['address1'];
        }


        if (isset($invoiceCustomFields['billing-city'])) {
            $billingCity = $invoiceCustomFields['billing-city'];
        } elseif (isset($savedBillingAddress['city'])) {
            $billingCity = $savedBillingAddress['city'];
        }

        if (isset($invoiceCustomFields['billing-state'])) {
            $billingState = $invoiceCustomFields['billing-state'];
        } elseif (isset($savedBillingAddress['stateShort'])) {
            $billingState = $savedBillingAddress['stateShort'];
        }

        if (isset($invoiceCustomFields['billing-code'])) {
            $billingZip = $invoiceCustomFields['billing-code'];
        }

        if (isset($invoiceCustomFields['billing-country'])) {
            $billingCountry = $invoiceCustomFields['billing-country'];
        } elseif (isset($savedBillingAddress['country'])) {
            $billingCountry = $savedBillingAddress['country'];
        }

        // Set the customer's Bill To address
        $customerAddress = new CustomerAddressType();
        if (!empty($contactCustomField['first-name'])) {
            $customerAddress->setFirstName($contactCustomField['first-name']);
        }
        if (!empty($contactCustomField['last-name'])) {
            $customerAddress->setLastName($contactCustomField['last-name']);
        }
        if (!empty($accountCustomField['account-name'])) {
            $customerAddress->setCompany($accountCustomField['account-name']);
        }


        if (!empty($billingAddress)) {
            $customerAddress->setAddress($billingAddress);
        }
        if (!empty($billingCity)) {
            $customerAddress->setCity($billingCity);
        }

        if (!empty($billingState)) {
            $customerAddress->setState($billingState);
        }

        if (!empty($billingZip)) {
            $customerAddress->setZip($billingZip);
        }
        if (!empty($billingCountry)) {
            $customerAddress->setZip($billingCountry);
        }

        // Set the customer's identifying information
        $customerData = new CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($invoice->contact_id);

        if (!empty($contactCustomField['email'])) {
            $customerAddress->setEmail($contactCustomField['email']);
        }


        // Add values for transaction settings
        $duplicateWindowSetting = new SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");


        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($invoice->grand_total);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);


        // Assemble the complete transaction request
        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId('ref_' . $invoice->getKey() . '_' . time());
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new CreateTransactionController($request);
        if (config('services.authorizeNet.mode') === 'live') {
            $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        } else {
            $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
        }


        //@todo  create proper logging

        if ($response != null) {
            $transactionResponse = $response->getTransactionResponse();

            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card

                if ($transactionResponse != null && $transactionResponse->getMessages() != null) {
                    Log::debug(
                        " Successfully created transaction with Transaction ID: " . $transactionResponse->getTransId(
                        ) . "\n",
                    );
                    Log::debug(" Transaction Response Code: " . $transactionResponse->getResponseCode() . "\n");
                    Log::debug(" Message Code: " . $transactionResponse->getMessages()[0]->getCode() . "\n");
                    Log::debug(" Auth Code: " . $transactionResponse->getAuthCode() . "\n");
                    Log::debug(" Description: " . $transactionResponse->getMessages()[0]->getDescription() . "\n");
                } else {
                    Log::error("Transaction Failed \n");
                    if ($transactionResponse->getErrors() != null) {
                        Log::error(" Error Code  : " . $transactionResponse->getErrors()[0]->getErrorCode() . "\n");
                        Log::error(" Error Message : " . $transactionResponse->getErrors()[0]->getErrorText() . "\n");
                    }
                }
            } else {
                Log::error("Transaction Failed \n");

                if ($transactionResponse != null && $transactionResponse->getErrors() != null) {
                    Log::error(" Error Code  : " . $transactionResponse->getErrors()[0]->getErrorCode() . "\n");
                    Log::error(" Error Message : " . $transactionResponse->getErrors()[0]->getErrorText() . "\n");
                } else {
                    Log::error(" Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n");
                    Log::error(" Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n");
                }
            }
        } else {
            Log::error("No response returned \n");
        }

        return $response;
    }
}
