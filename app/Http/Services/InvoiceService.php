<?php

namespace App\Http\Services;

use App\Exceptions\ChangedAddressErrorException;
use App\Exceptions\CustomErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Helpers\StorageHelper;
use App\Http\Repositories\AccountRepository;
use App\Http\Repositories\ContactRepository;
use App\Http\Repositories\InvoiceAttachmentRepository;
use App\Http\Repositories\InvoiceItemRepository;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\ProductRepository;
use App\Http\Resource\InvoiceResource;
use App\Jobs\GenerateStripeInvoiceWithSubscription;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomFieldValues;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceItem;
use App\Models\InvoiceStatusLog;
use App\Models\User;
use App\Traits\FillShippingAndBillingAddressTrait;
use App\Traits\TaxableModelTotals;
use App\Traits\TaxCalculationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use net\authorize\api\contract\v1\AnetApiResponseType;
use Stripe\Exception\ApiErrorException;
use Throwable;

class InvoiceService extends BaseService
{
    use TaxCalculationTrait;
    use FillShippingAndBillingAddressTrait;
    use TaxableModelTotals;

    private InvoiceAttachmentRepository $invoiceAttachmentRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceAttachmentRepository $invoiceAttachmentRepository,
        private AccountRepository $accountRepository,
        private ContactRepository $contactRepository,
        private StripeService $stripeService,
        private AuthorizeNetService $authorizeNetService,
        private ProductRepository $productRepository,
        private InvoiceItemRepository $invoiceItemRepository,
    ) {
        $this->repository = $invoiceRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
    }

    public function resource(): string
    {
        return InvoiceResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate(
            $this->repository->get($user, $params, [
                'estimate',
                'estimate.customFields',
                'account',
                'account.customFields',
                'account.customFields.customField',
            ]),
        );
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['sub_total'] = 0;
        $data['total_tax'] = 0;
        $data['total_discount'] = 0;
        $data['grand_total'] = 0;
        $data['balance_due'] = 0;

        if (!isset($data['estimate_id'])) {
            $data = $this->fillAddressDataFromAccount($data);
        }

        return $data;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param Authenticatable|User $user
     * @return void
     * @throws CustomErrorException
     */
    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Invoice $model */
        InvoiceStatusLog::query()->create([
            'invoice_id' => $model->getKey(),
            'status' => $model->status,
        ]);

        $this->invoiceItemRepository->saveInvoiceItems($data['itemGroups'], $model->getKey());
        $this->updateModelTotals($data, $model);

        //  list($subTotal, $totalDiscount) = $this->insertInvoiceItemAndCalculateTotal($data['itemGroups'], $model);


        $fileName = 'INV_' . date('Y') . '_' . $model->getKey();
        $model->filename = $fileName;
        $model->invoice_number = 'INV_' . date('Y') . $model->getKey();

        //  $account = $this->accountRepository->findById($data['account_id']);
        //  $contact = $this->contactRepository->findById($data['contact_id']);

        /** @var CustomFieldValues $contactStateValue */
        /*       $contactStateValue = CustomFieldValues::query()->where('entity', Contact::class)
                 ->where('entity_id', $contact->getKey())->whereHas('customField', function ($query) {
                     $query->where('entity_type', Contact::class)->where('code', 'contact-state');
                 })->f();

             /*      $contactState = '';
                  if ($contactStateValue) {
                      $contactState = $contactStateValue->text_value;
               }

                  $totalTax = $this->calculateTax($subTotal, $contactState, $account->getKey());
                  $grandTotal = $subTotal + $totalTax - $totalDiscount;
                  $model->sub_total = $subTotal;
                  $model->total_tax = $totalTax;
                  $model->grand_total = $grandTotal;
                  $model->total_discount = $totalDiscount;  */
        $model->save();

        GenerateStripeInvoiceWithSubscription::dispatch($model, auth()->user());
        parent::afterStore($model, $data, $user);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param Authenticatable|User $user
     * @return void
     * @throws CustomErrorException
     */
    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        $changedValues = $model->getChanges();

        /** @var Invoice $model */
        if (isset($changedValues['status'])) {
            InvoiceStatusLog::query()->create([
                'invoice_id' => $model->getKey(),
                'status' => $model->status,
            ]);

            switch ($changedValues['status']) {
                case Invoice::INVOICE_STATUS_TERMS_ACCEPTED:
                    $model->terms_accepted_at = now();
                    $model->save();
                    break;


                case Invoice::INVOICE_STATUS_OPEN:
                    $model->opened_at = now();
                    $model->save();
                    break;

                case Invoice::INVOICE_STATUS_SENT:
                    $model->sent_at = now();
                    $model->save();
                    break;
            }
        }

        if (!isset($data['itemGroups'])) {
            return;
        }
        InvoiceItem::query()->where('invoice_id', $model->getKey())->delete();


        $this->invoiceItemRepository->saveInvoiceItems($data['itemGroups'], $model->getKey());
        $this->updateModelTotals($data, $model);
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Invoice $invoice, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->invoiceAttachmentRepository->create([
                'invoice_id' => $invoice->getKey(),
                'name' => $data['name'] ?? '',
                'attachment_link' => $data['link'],
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/invoice/' . $invoice->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->invoiceAttachmentRepository->create([
                        'invoice_id' => $invoice->getKey(),
                        'name' => $data['name'] ?? '',
                        'attachment_file' => $savedFile,
                        'created_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @param InvoiceAttachment $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Invoice $invoice,
        InvoiceAttachment $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->invoiceAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'name' => $data['name'] ?? '',
                'attachment_file' => null,
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/invoice/' . $invoice->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->invoiceAttachmentRepository->update($attachment, [
                        'attachment_file' => $savedFile,
                        'name' => $data['name'] ?? '',
                        'attachment_link' => null,
                        'updated_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    /**
     * @param InvoiceAttachment $attachment
     * @return void
     */
    public function deleteAttachment(InvoiceAttachment $attachment): void
    {
        $this->invoiceAttachmentRepository->delete($attachment);
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            'customFields',
            'invoicePayments',
            'attachments',
            'opportunity',
            'opportunity.customFields',
            'opportunity.customFields.customField',
            'client',
            'client.customFields',
            'client.customFields.customField',
            'client.account',
            'client.account.customFields',
            'client.account.customFields.customField',
            'client.account.contacts',
            'client.account.contacts.customFields',
            'client.account.contacts.customFields.customField',
            'client.account.contacts.customFields.relatedContactType',
            'client.account.contacts.customFields.relatedUser',
            'client.account.contacts.createdBy',
            'opportunity',
            'opportunity.customFields',
            'opportunity.customFields.customField',
            'opportunity.customFields.relatedContact',
            'opportunity.customFields.relatedContact.customFields',
            'opportunity.customFields.relatedContact.customFields.customField',
            'opportunity.customFields.relatedContact.account',
            'opportunity.customFields.relatedContact.account.customFields',
            'opportunity.customFields.relatedContact.account.customFields.customField',
            'opportunity.customFields.relatedContact.account.contacts',
            'opportunity.customFields.relatedContact.account.contacts.customFields',
            'opportunity.customFields.relatedContact.account.contacts.customFields.customField',
            'opportunity.customFields.relatedContact.account.contacts.customFields.relatedContactType',
            'opportunity.customFields.relatedContact.account.contacts.customFields.relatedUser',
            'opportunity.customFields.relatedContact.account.contacts.createdBy',
            'account',
            'account.contacts',
            'account.contacts.customFields',
            'account.contacts.customFields.customField',
            'account.customFields',
            'account.customFields.customField',
            'invoiceItemGroup',
            'invoiceItemGroup.contact',
            'invoiceItemGroup.items',
            'invoiceItemGroup.items.product',
            'invoiceItemGroup.items.product.customFields',
            'invoiceItemGroup.items.product.customFields.customField',
            'statusLog',
        );

        return parent::show($model, $resource);
    }

    /**
     * @param Invoice $invoice
     * @return array
     * @throws CustomErrorException
     * @throws ApiErrorException
     */
    public function getStripeClientSecret(Invoice $invoice): array
    {
        if (empty($invoice->stripe_invoice_id)) {
            throw new CustomErrorException('Stripe invoice not exist', 422);
        }

        $stripeInvoice = $this->stripeService->getInvoice($invoice->stripe_invoice_id);

        if ($stripeInvoice->status == 'draft') {
            $stripeInvoice = $this->stripeService->finalizeInvoice($invoice->stripe_invoice_id);
        }

        return ['clientSecret' => $stripeInvoice->payment_intent->client_secret];
    }

    public function getAuthorizeTransactionDetails(Invoice $invoice, array $data): ?AnetApiResponseType
    {
        return $this->authorizeNetService->createAnAcceptPaymentTransaction($invoice, $data);
    }

    /**
     * Workflow 6 #58
     * @param array $data
     * @return array
     */
    private function fillAddressDataFromAccount(array $data): array
    {
        $savedBillingAddress = [];
        $savedShippingAddress = [];

        /** @var Account $account */
        $account = Account::query()->find($data['account_id']);
        $this->convertSavedAddressArray($account, $savedBillingAddress, $savedShippingAddress);

        return $this->fillSavedBillingAndShippingAddresses($data, $savedBillingAddress, $savedShippingAddress);
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return array
     * @throws ChangedAddressErrorException
     */
    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        /** @var Invoice $model */
        if (
            isset($data['contact_id']) &&
            $data['contact_id'] !== $model->contact_id &&
            (!isset($data['ignoreAddressChanges']) || !$data['ignoreAddressChanges'])
        ) {
            //check if new contact addresses is different from saved

            $savedInvoiceAddresses = CustomFieldValuesHelper::getCustomFieldValues($model, [
                'billing-street',
                'billing-city',
                'billing-state',
                'billing-code',
                'billing-country',
                'shipping-street',
                'shipping-city',
                'shipping-state',
                'shipping-code',
                'shipping-country',
            ]);

            $savedBillingAddress = [];
            $savedShippingAddress = [];

            /** @var Contact $contact */
            $contact = Contact::query()->find($data['contact_id']);
            $this->convertSavedAddressArray($contact, $savedBillingAddress, $savedShippingAddress);


            $changedAddressValues = [];
            if (!empty($savedBillingAddress) || !empty($savedShippingAddress)) {
                $billingAddressFieldsForEstimateToContactRelation = [
                    'billing-street' => 'address1',
                    'billing-city' => 'city',
                    'billing-state' => 'stateShort',
                    'billing-country' => 'country',
                    'billing-code' => 'zipCode',
                ];

                $shippingAddressFieldsForEstimateToContactRelation = [
                    'shipping-street' => 'address1',
                    'shipping-city' => 'city',
                    'shipping-state' => 'stateShort',
                    'shipping-country' => 'country',
                    'shipping-code' => 'zipCode',
                ];

                foreach ($billingAddressFieldsForEstimateToContactRelation as $key => $value) {
                    if (!isset($savedBillingAddress[$value])) {
                        continue;
                    }
                    if (
                        empty($savedInvoiceAddresses[$key]) ||
                        $savedInvoiceAddresses[$key] !== $savedBillingAddress[$value]
                    ) {
                        if (!empty($savedBillingAddress[$value]) && !isset($data['customFields'][$key])) {
                            $changedAddressValues[$key] = [
                                'saved' => $savedInvoiceAddresses[$key] ?? null,
                                'newContact' => $savedBillingAddress[$value],
                            ];
                        }
                    }
                }

                foreach ($shippingAddressFieldsForEstimateToContactRelation as $key => $value) {
                    if (!isset($savedShippingAddress[$value])) {
                        continue;
                    }
                    if (
                        empty($savedInvoiceAddresses[$key]) ||
                        $savedInvoiceAddresses[$key] !== $savedShippingAddress[$value]
                    ) {
                        if (!empty($savedShippingAddress[$value])) {
                            $changedAddressValues[$key] = [
                                'saved' => $savedInvoiceAddresses[$key] ?? null,
                                'newContact' => $savedShippingAddress[$value],
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($changedAddressValues)) {
            throw new ChangedAddressErrorException($changedAddressValues, 'Address details changed', 422);
        }
        $data['entity_type'] = Invoice::class;
        $data['updated_by'] = $user->getKey();

        return $data;
    }

    public function sendInvoice(Invoice $invoice): void
    {
        $contact = $invoice->contact;
        CustomFieldValuesHelper::getCustomFieldValues($contact, ['email']);
        //@todo send email to contact with invoice details
    }
}
