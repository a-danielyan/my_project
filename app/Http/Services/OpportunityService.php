<?php

namespace App\Http\Services;

use App\Events\ModelChanged;
use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\ActivityRepository;
use App\Http\Repositories\OpportunityAttachmentRepository;
use App\Http\Repositories\OpportunityRepository;
use App\Http\Resource\OpportunityResource;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\CustomFieldValues;
use App\Models\EntityLog;
use App\Models\Opportunity;
use App\Models\OpportunityAttachment;
use App\Models\OpportunityStageLogs;
use App\Models\Stage;
use App\Models\User;
use App\Traits\FillShippingAndBillingAddressTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use Throwable;

class OpportunityService extends BaseService
{
    use FillShippingAndBillingAddressTrait;

    public function __construct(
        OpportunityRepository $opportunityRepository,
        private OpportunityAttachmentRepository $opportunityAttachmentRepository,
        private ActivityRepository $activityRepository,
    ) {
        $this->repository = $opportunityRepository;
    }

    public function resource(): string
    {
        return OpportunityResource::class;
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
            $this->repository->getAllWithCustomFields($params, $user, [
                'account',
                'account.customFields',
                'account.customFields.customField',
                'account.contacts',
                'account.contacts.customFields',
                'account.contacts.customFields.customField',
                'account.contacts.customFields.relatedContactType',
                'account.contacts.customFields.relatedUser',
                'account.contacts.createdBy',

            ]),
        );
    }

    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Opportunity::class;

        return $this->addMissedAddresses($data);
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model->load(
            [
                'customFields',
                'customFields.customField',
                'stageLog',
                'stageLog.stage',
                'stage',
                'tag',
                'estimates',
                'estimates.customFields',
                'estimates.customFields.customField',
                'estimates.contact',
                'estimates.contact.customFields',
                'estimates.contact.customFields.customField',
                'attachments',
                'account',
                'account.customFields',
                'account.customFields.customField',
                'account.contacts',
                'account.contacts.customFields',
                'account.contacts.customFields.customField',
                'account.contacts.customFields.relatedContactType',
                'account.contacts.customFields.relatedUser',
                'account.contacts.createdBy',
                'internalNote',
            ],
        );
        $resource = $resource ?? $this->resource();

        return new $resource($model);
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();
        $data['entity_type'] = Opportunity::class;

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
        /** @var Opportunity $model */
        OpportunityStageLogs::query()->create([
            'opportunity_id' => $model->getKey(),
            'stage_id' => $model->stage_id,
        ]);

        if (!empty($data['internalNotes'])) {
            if (!isset($data['customFields']['opportunity-owner'])) {
                throw new CustomErrorException('opportunity-owner custom field required', 422);
            }
            $activityNoteData = [
                'related_to' => $data['customFields']['opportunity-owner'],
                'subject' => 'New opportunity Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Opportunity::class,
                'related_to_id' => $model->getKey(),
                'activity_status' => 'Not started',
                'activity_type' => Activity::ACTIVITY_TYPE_INTERNAL_NOTE,
                'created_by' => $model->created_by,
            ];
            $activity = $this->activityRepository->create($activityNoteData);

            $changedEntityLog = [
                'entity' => Opportunity::class,
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
        $changedValues = $model->getChanges();

        /** @var Opportunity $model */
        if (isset($changedValues['stage_id'])) {
            OpportunityStageLogs::query()->create([
                'opportunity_id' => $model->getKey(),
                'stage_id' => $data['stage_id'],
            ]);
            /** @var Stage $stage */
            $stage = Stage::query()->find($changedValues['stage_id']);
            if (
                in_array(
                    $stage->name,
                    [Stage::CLOSED_WON_STAGE, Stage::CLOSED_LOST_STAGE],
                )
            ) {
                $model->closed_at = now();
                $model->save();
            }
        }

        if (!empty($data['internalNotes'])) {
            if (isset($data['customFields']['opportunity-owner'])) {
                $opportunityOwner = $data['customFields']['opportunity-owner'];
            } else {
                /** @var  CustomFieldValues $opportunityOwnerCustomFieldValue */
                $opportunityOwnerCustomFieldValue = CustomFieldValues::query()->where('entity_id', $model->getKey())
                    ->where('entity', Opportunity::class)->whereHas('customField', function ($query) {
                        $query->where('entity_type', Opportunity::class)->where('code', 'opportunity-owner');
                    })->first();
                $opportunityOwner = $opportunityOwnerCustomFieldValue->integer_value;
            }

            $activityNoteData = [
                'related_to' => $opportunityOwner,
                'subject' => 'New Opportunity Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Opportunity::class,
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

    /**
     * @param array $data
     * @param Opportunity $opportunity
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Opportunity $opportunity, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->opportunityAttachmentRepository->create([
                'opportunity_id' => $opportunity->getKey(),
                'name' => $data['name'] ?? '',
                'attachment_link' => $data['link'],
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/opportunity/' . $opportunity->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->opportunityAttachmentRepository->create([
                        'opportunity_id' => $opportunity->getKey(),
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
     * @param Opportunity $opportunity
     * @param OpportunityAttachment $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Opportunity $opportunity,
        OpportunityAttachment $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->opportunityAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'attachment_file' => null,
                'name' => $data['name'] ?? '',
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/opportunity/' . $opportunity->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->opportunityAttachmentRepository->update($attachment, [
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

    public function deleteAttachment(OpportunityAttachment $attachment): void
    {
        $this->opportunityAttachmentRepository->delete($attachment);
    }

    /**
     * Addresses workflow 3.1 and 3.2  #58
     * @param array $data
     * @return array
     * @throws CustomErrorException
     */
    private function addMissedAddresses(array $data): array
    {
        $savedBillingAddress = [];
        $savedShippingAddress = [];

        if (isset($data['customFields']['contact-name'])) {
            /** @var Contact $contact */
            $contact = Contact::query()->find($data['customFields']['contact-name']);

            $this->convertSavedAddressArray($contact, $savedBillingAddress, $savedShippingAddress);
        }

        if (empty($savedBillingAddress) && empty($savedShippingAddress)) {
            /** @var Account $account */
            $account = Account::query()->find($data['account_id']);
            $this->convertSavedAddressArray(
                $account,
                $savedBillingAddress,
                $savedShippingAddress,
            );
        }

        return $this->fillSavedBillingAndShippingAddresses($data, $savedBillingAddress, $savedShippingAddress);
    }
}
