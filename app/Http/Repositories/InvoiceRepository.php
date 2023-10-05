<?php

namespace App\Http\Repositories;

use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\Interfaces\FilteredInterface;
use App\Models\Interfaces\FilterStrategy;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvoiceRepository extends BaseRepositoryWithCustomFields
{
    /**
     * @param Invoice $invoice
     * @param CustomFieldValueRepository $customFieldValueRepository
     * @param CustomFieldRepository $customFieldRepository
     */
    public function __construct(
        Invoice $invoice,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        parent::__construct($invoice, $customFieldValueRepository, $customFieldRepository);
    }

    public function updateSentInvoicesToPaymentPendingStatus(): void
    {
        $this->model
            ->where('status', Invoice::INVOICE_STATUS_SENT)
            ->where('sent_at', '<', now()->subDay())
            ->update(['status' => Invoice::INVOICE_STATUS_PAYMENT_PENDING]);
    }

    protected function prepareQueryForGet(
        array $params = [],
        ?User $user = null,
        Model $customModel = null,
        bool $forCount = false,
    ): Builder {
        $model = $customModel ?? $this->model;

        $query = $model->newQuery();

        $query->with([
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
        ]);

        if (!empty($params) && $model instanceof FilteredInterface) {
            /** @var FilteredInterface $query */
            $query->filter($params);
        }

        $query = self::filterQueryByPermission($query, $user, $params['hasWritePermission'] ?? false);

        if (
            array_key_exists('distinct', $params) &&
            !empty($params['fields']) && $model instanceof FilteredInterface
        ) {
            if ($this->model instanceof FilterStrategy) {
                $filterArray = $this->model->filterStrategy()->filterArray();
            } else {
                $filterArray = $this->model->filterArray();
            }
            foreach (explode(',', $params['fields']) as $field) {
                $field = trim($field);
                $snakeField = Str::snake($field);
                if (
                    in_array($field, $filterArray['equal']) || in_array($field, $filterArray['like'])
                    || in_array($snakeField, $filterArray['equal']) || in_array($snakeField, $filterArray['like'])
                ) {
                    $query->groupBy($snakeField);
                } elseif (isset($filterArray['field_relation'][$field])) {
                    $relationFunction = $filterArray['field_relation'][$field]['relationFunction'];
                    $model->{$relationFunction}($query, $field);
                }
            }
        } else {
            if (!$forCount) {
                $query->groupBy($model->getTable() . '.id');
            }
        }

        if ($model instanceof FilteredInterface) {
            if (!$forCount) {
                $query->orderBy($model->getTable() . '.id');
            }
        }

        return $query;
    }
}
