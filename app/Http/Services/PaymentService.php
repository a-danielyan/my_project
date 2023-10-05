<?php

namespace App\Http\Services;

use App\Http\Repositories\PaymentRepository;
use App\Http\Resource\PaymentResource;
use App\Jobs\UpdateInvoiceBalanceDue;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentService extends BaseService
{
    private int $oldInvoiceId = 0;

    public function __construct(
        PaymentRepository $paymentRepository,
    ) {
        $this->repository = $paymentRepository;
    }

    public function resource(): string
    {
        return PaymentResource::class;
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['payment_name'] = 'PAYMENT_' . date('Ymd');

        return $data;
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        /** @var Payment $model */
        $this->oldInvoiceId = $model->invoice_id;

        return $data;
    }


    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate(
            $this->repository->get($user, $params, [
                'invoice',
                'account',
                'account.customFields',
                'account.customFields.customField',
            ]),
        );
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            'invoice',
            'account',
            'account.customFields',
            'account.customFields.customField',
        );

        return parent::show($model, $resource);
    }

    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Payment $model */
        UpdateInvoiceBalanceDue::dispatch($model->invoice_id);
        parent::afterStore($model, $data, $user);
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Payment $model */
        $changedValues = $model->getChanges();

        if (isset($changedValues['invoice_id'])) {
            UpdateInvoiceBalanceDue::dispatch($model->invoice_id);
            UpdateInvoiceBalanceDue::dispatch($this->oldInvoiceId);
        }
    }
}
