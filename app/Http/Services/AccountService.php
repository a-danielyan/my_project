<?php

namespace App\Http\Services;

use App\Events\ModelChanged;
use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\AccountAttachmentRepository;
use App\Http\Repositories\AccountRepository;
use App\Http\Repositories\ActivityRepository;
use App\Http\Resource\AccountResource;
use App\Models\Account;
use App\Models\AccountAttachment;
use App\Models\Activity;
use App\Models\CustomFieldValues;
use App\Models\EntityLog;
use App\Models\User;
use App\Traits\SyncDataFromApolloTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountService extends BaseService
{
    use SyncDataFromApolloTrait;

    private AccountAttachmentRepository $accountAttachmentRepository;

    public const APOLLO_TO_CUSTOM_FIELD_MAPPING = [
        'name' => 'account-name',
        'website_url' => 'website',
        'linkedin_url' => 'linkedin_url',
        'phone' => 'phone',
        'industry' => 'industry',
        'short_description' => 'short_description',
    ];

    public function __construct(
        AccountRepository $accountRepository,
        AccountAttachmentRepository $accountAttachmentRepository,
        private ApolloIoService $apolloIoService,
        private ActivityRepository $activityRepository,
    ) {
        $this->repository = $accountRepository;
        $this->accountAttachmentRepository = $accountAttachmentRepository;
    }

    public function resource(): string
    {
        return AccountResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate(
            $this->repository->getAllWithCustomFields(
                $params,
                $user,
                [
                    'invoices',
                    'opportunities',
                    'accountsPayable',
                ],
            ),
        );
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Account::class;

        return $data;
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['entity_type'] = Account::class;
        $data['updated_by'] = $user->getKey();

        if (isset($data['avatarFile'])) {
            try {
                $savePath = '/account/' . $model->getKey() . '/avatar';
                $savedFile = StorageHelper::storeFile($data['avatarFile'], $savePath);
                $data['avatar'] = $savedFile;
            } catch (Throwable $e) {
                throw new CustomErrorException($e->getMessage(), 422);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param Account $account
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Account $account, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->accountAttachmentRepository->create([
                'account_id' => $account->getKey(),
                'name' => $data['name'] ?? '',
                'attachment_link' => $data['link'],
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/account/' . $account->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->accountAttachmentRepository->create([
                        'account_id' => $account->getKey(),
                        'attachment_file' => $savedFile,
                        'name' => $data['name'] ?? '',
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
     * @param Account $account
     * @param AccountAttachment $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Account $account,
        AccountAttachment $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->accountAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'attachment_file' => null,
                'name' => $data['name'] ?? '',
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/account/' . $account->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->accountAttachmentRepository->update($attachment, [
                        'attachment_file' => $savedFile,
                        'attachment_link' => null,
                        'name' => $data['name'] ?? '',
                        'updated_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    public function deleteAttachment(AccountAttachment $attachment): void
    {
        $this->accountAttachmentRepository->delete($attachment);
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            'customFields',
            'customFields.customField',
            'attachments',
            'tag',
            'invoices',
            'opportunities',
            'internalNote',
            'lead',
            'contacts',
            'contacts.customFields',
            'contacts.customFields.customField',
            'contacts.customFields.relatedContactType',
            'contacts.customFields.relatedUser',
            'contacts.createdBy',
        );

        return parent::show($model, $resource);
    }

    /**
     * @param Account $account
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(
        Account $account,
        User|Authenticatable $user,
    ): void {
        $this->syncOrganizationDataFromApollo($account, $user);
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
        /** @var Account $model */
        if (!empty($data['internalNotes'])) {
            if (!isset($data['customFields']['account-owner'])) {
                throw new CustomErrorException('account-owner custom field required', 422);
            }
            $activityNoteData = [
                'related_to' => $data['customFields']['account-owner'],
                'subject' => 'New Account Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Account::class,
                'related_to_id' => $model->getKey(),
                'activity_status' => 'Not started',
                'activity_type' => Activity::ACTIVITY_TYPE_INTERNAL_NOTE,
                'created_by' => $model->created_by,
            ];
            $activity = $this->activityRepository->create($activityNoteData);

            $changedEntityLog = [
                'entity' => Account::class,
                'entity_id' => $model->getKey(),
                'field_id' => null,
                'previous_value' => null,
                'new_value' => 'Activity created',
                'updated_by' => $user->getKey(),
                'update_id' => time(),
                'created_at' => now(),
                'log_type' => EntityLog::NOTE_LOG_TYPE,
                'activity_id' => $activity->getKey(),
            ];
            ModelChanged::dispatch($changedEntityLog);
        }
        parent::afterStore($model, $data, $user);
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        if (!empty($data['internalNotes'])) {
            if (isset($data['customFields']['account-owner'])) {
                $accountOwner = $data['customFields']['account-owner'];
            } else {
                /** @var  CustomFieldValues $accountOwnerCustomFieldValue */
                $accountOwnerCustomFieldValue = CustomFieldValues::query()->where('entity_id', $model->getKey())
                    ->where('entity', Account::class)->whereHas('customField', function ($query) {
                        $query->where('entity_type', Account::class)->where('code', 'account-owner');
                    })->first();
                $accountOwner = $accountOwnerCustomFieldValue->integer_value;
            }
            /** @var Account $model */
            $activityNoteData = [
                'related_to' => $accountOwner,
                'subject' => 'New Account Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Account::class,
                'related_to_id' => $model->getKey(),
                'activity_status' => 'Not started',
                'activity_type' => Activity::ACTIVITY_TYPE_INTERNAL_NOTE,
                'created_by' => $model->created_by,
            ];
            $this->activityRepository->updateOrCreate(
                ['related_to_id' => $model->getKey(), 'activity_type' => Activity::ACTIVITY_TYPE_INTERNAL_NOTE],
                $activityNoteData,
            );
        }
    }
}
