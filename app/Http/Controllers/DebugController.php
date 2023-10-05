<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ZohoAPILimitException;
use App\Http\Services\ZohoBooksService;
use App\Http\Services\ZohoService;
use App\Models\OauthToken;
use App\Models\ZohoEntityExport;
use com\zoho\crm\api\bulkread\ActionWrapper;
use com\zoho\crm\api\bulkread\APIException;
use com\zoho\crm\api\bulkread\BulkReadOperations;
use com\zoho\crm\api\bulkread\CallBack;
use com\zoho\crm\api\bulkread\Query;
use com\zoho\crm\api\bulkread\RequestWrapper;
use com\zoho\crm\api\bulkread\SuccessResponse;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\modules\MinifiedModule;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\record\ResponseWrapper;
use com\zoho\crm\api\record\SearchRecordsParam;
use com\zoho\crm\api\record\UpdateRecordHeader;
use com\zoho\crm\api\util\APIResponse;
use com\zoho\crm\api\util\Choice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Webleit\ZohoBooksApi\ZohoBooks;

/**
 * @codeCoverageIgnore
 * This controller used only for debug purpose and can be removed safely
 */
class DebugController
{
    public function __construct(private ZohoService $zohoService)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws SDKException
     */
    public function getNotification(Request $request): JsonResponse
    {
        return response()->json($this->zohoService->getNotification($request->get('channelId')));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws SDKException
     */
    public function makeApiBulk(Request $request): JsonResponse
    {
        /** @var ZohoService $zohoService */
        $zohoService = resolve(ZohoService::class);
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $zohoService->initializeZoho($token->grant_token);

        $moduleAPIName = $request->get('moduleName');
        if (empty($moduleAPIName)) {
            return response()->json(['error' => 'Wrong module name']);
        }

        $response = $this->makeBulkApiRequest($moduleAPIName);


        $actionHandler = $response->getObject();
        $error = '';
        $result = '';

        if ($actionHandler instanceof ActionWrapper) {
            $actionWrapper = $actionHandler;
            $actionResponses = $actionWrapper->getData();

            foreach ($actionResponses as $actionResponse) {
                if ($actionResponse instanceof SuccessResponse) {
                    $result = 'success';
                } else {
                    if ($actionResponse instanceof APIException) {
                        $error = $this->handleErrorResponse($actionResponse);
                    }
                }
            }
        } else {
            if ($actionHandler instanceof APIException) {
                $error = $this->handleErrorResponse($actionHandler);
            }
        }


        return response()->json(['error' => $error, 'result' => $result]);
    }

    private function handleErrorResponse(APIException $actionResponse): string
    {
        $errorMessage = "Status: " . $actionResponse->getStatus()->getValue() . "\n";
        $errorMessage .= "Code: " . $actionResponse->getCode()->getValue() . "\n";
        $errorMessage .= "Details: ";
        foreach ($actionResponse->getDetails() as $key => $value) {
            $errorMessage .= $key . " : " . $value . "\n";
        }
        $errorMessage .= "Message: " . $actionResponse->getMessage()->getValue() . "\n";

        return $errorMessage;
    }


    /**
     * @param string $moduleAPIName
     * @return APIResponse
     * @throws CustomErrorException
     */
    private function makeBulkApiRequest(string $moduleAPIName): APIResponse
    {
        try {
            $bulkReadOperations = new BulkReadOperations();
            $requestWrapper = new RequestWrapper();
            $callback = new CallBack();
            $callback->setUrl(config('services.zohoCrm.notification'));
            $callback->setMethod(new Choice("post"));
            $requestWrapper->setCallback($callback);

            $query = new Query();
            $module = new MinifiedModule();
            $module->setAPIName($moduleAPIName);
            $query->setModule($module);
            $query->setPage(1);
            $requestWrapper->setQuery($query);

            return $bulkReadOperations->createBulkReadJob($requestWrapper);
        } catch (Throwable $e) {
            throw new CustomErrorException($e->getMessage(), 422);
        }
    }

    public function conversionStats(): JsonResponse
    {
        $exportStats = ZohoEntityExport::query()->selectRaw('COUNT(*) AS TotalRecords,sync_status')->groupBy(
            'sync_status',
        )->get();

        $topErrors = ZohoEntityExport::query()->selectRaw('COUNT(*) AS TotalRecords,error')->groupBy(
            'error',
        )->where('sync_status', 'ERROR')->orderBy('TotalRecords', 'desc')->get();


        return response()->json(['exportStats' => $exportStats, 'topErrors' => $topErrors]);
    }

    /**
     * @param Request $request
     * @return void
     * @throws CustomErrorException
     */
    public function checkZohoBooks(Request $request): void
    {
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $zohoBooksService = resolve(ZohoBooksService::class);
        $zohoBooksClient = $zohoBooksService->initializeZoho($token);

// Create the main class
        $zohoBooks = new ZohoBooks($zohoBooksClient);

        try {
            $zohoBooksService->validateRequestUsages();
        } catch (ZohoAPILimitException) {
            throw new CustomErrorException('API limit reached', 422);
        }


        switch ($request->get('action')) {
            case 'org':
                try {
                    $org = $zohoBooks->organizations->getDefaultOrganization();
                    print_r($org); // 401
                } catch (Throwable $e) {
                    echo $e->getMessage();
                }

                break;

            case 'modules':
                /**
                 * Available ZohoBooksModules
                 * Array
                 * (
                 * [contacts] => Webleit\ZohoBooksApi\Modules\Contacts
                 * [estimates] => Webleit\ZohoBooksApi\Modules\Estimates
                 * [salesorders] => Webleit\ZohoBooksApi\Modules\SalesOrders
                 * [invoices] => Webleit\ZohoBooksApi\Modules\Invoices
                 * [recurringinvoices] => Webleit\ZohoBooksApi\Modules\RecurringInvoices
                 * [creditnotes] => Webleit\ZohoBooksApi\Modules\CreditNotes
                 * [customerpayments] => Webleit\ZohoBooksApi\Modules\CustomerPayments
                 * [expenses] => Webleit\ZohoBooksApi\Modules\Expenses
                 * [recurringexpenses] => Webleit\ZohoBooksApi\Modules\RecurringExpenses
                 * [purchaseorders] => Webleit\ZohoBooksApi\Modules\PurchaseOrders
                 * [bills] => Webleit\ZohoBooksApi\Modules\Bills
                 * [vendorcredits] => Webleit\ZohoBooksApi\Modules\VendorCredits
                 * [vendorpayments] => Webleit\ZohoBooksApi\Modules\VendorPayments
                 * [bankaccounts] => Webleit\ZohoBooksApi\Modules\BankAccounts
                 * [banktransactions] => Webleit\ZohoBooksApi\Modules\BankTransactions
                 * [bankrules] => Webleit\ZohoBooksApi\Modules\BankRules
                 * [chartofaccounts] => Webleit\ZohoBooksApi\Modules\ChartOfAccounts
                 * [journals] => Webleit\ZohoBooksApi\Modules\Journals
                 * [basecurrencyadjustment] => Webleit\ZohoBooksApi\Modules\BaseCurrencyAdjustment
                 * [projects] => Webleit\ZohoBooksApi\Modules\Projects
                 * [settings] => Webleit\ZohoBooksApi\Modules\Settings
                 * [organizations] => Webleit\ZohoBooksApi\Modules\Organizations
                 * [items] => Webleit\ZohoBooksApi\Modules\Items
                 * [users] => Webleit\ZohoBooksApi\Modules\Users
                 * [import] => Webleit\ZohoBooksApi\Modules\Import
                 * )
                 */
                break;

            case 'salesorders':
                if (!empty($request->get('sales_order_name'))) {
                    $salesOrder = $zohoBooks->salesorders
                        ->getList(['search_text' => $request->get('sales_order_name')]);

                    foreach ($salesOrder as $order) {
                        $soData = $order->getData();
                        print_r($soData);
                    }

                    exit();
                }


                $singleSalesOrder = $zohoBooks->salesorders->get(4499820000157127313);
                $soData = $singleSalesOrder->getData();
                print_r($soData);


                break;
            case 'invoices':
                try {
                    if (!empty($request->get('invoice_name_name'))) {
                        $invoiceByNumber = $zohoBooks->invoices
                            ->getList(['invoice_number' => $request->get('invoice_name_name')]);

                        print_r($invoiceByNumber);

                        exit();
                    }
                    $listInvoices = $zohoBooks->invoices->getList(['per_page' => 1]);

                    foreach ($listInvoices as $invoice) {
                        $singleInvoice = $zohoBooks->invoices->get($invoice->invoice_id);


                        $invoiceData = $singleInvoice->getData();

                        print_r($invoiceData);

                        exit();
                    }
                } catch (Throwable $e) {
                    echo $e->getMessage();
                }
                break;

            case 'recuring':
                try {
                    $listRecurringInvoices = $zohoBooks->recurringinvoices->getList();
                    print_r($listRecurringInvoices);
                } catch (Throwable $e) {
                    echo $e->getMessage();
                }

                break;

            case 'payment':
                try {
                    $payments = $zohoBooks->customerpayments->getList();
                    print_r($payments); //401
                } catch (Throwable $e) {
                    echo $e->getMessage();
                }

                break;

            default:
                echo 'no parameter';
                break;
        }
    }

    public function addProductBySku(Request $request)
    {
        /** @var ZohoService $zohoService */
        $zohoService = resolve(ZohoService::class);
        /** @var OauthToken $token */
        $token = OauthToken::query()->where('service', 'zohocrm')->first();
        $zohoService->initializeZoho($token->grant_token);

        $moduleAPIName = 'Products';

        $recordOperations = new RecordOperations();
        $paramInstance = new ParameterMap();
        $criteria = "((Product_Code:starts_with:" . $request->sku . "))";

        $paramInstance->add(SearchRecordsParam::criteria(), $criteria);
        $paramInstance->add(SearchRecordsParam::page(), 1);
        $paramInstance->add(SearchRecordsParam::perPage(), 20);
        //     $headerInstance = new HeaderMap();
        //    $headerInstance->add(UpdateRecordHeader::XEXTERNAL(), "Leads.External");
        //Call searchRecords method
        $response = $recordOperations->searchRecords($moduleAPIName, $paramInstance);
        if ($response->isExpected()) {
            $responseHandler = $response->getObject();
            if ($responseHandler instanceof ResponseWrapper) {
                $responseWrapper = $responseHandler;
                $records = $responseWrapper->getData();
                if ($records != null) {
                    foreach ($records as $record) {
                        echo("Record ID: " . $record->getId() . "\n");
                    }
                }
            }
        }
    }
}
