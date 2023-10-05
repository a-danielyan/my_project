<?php

namespace App\Http\Services;

use App\Events\ModelChanged;
use App\Events\ModelDeleted;
use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\ActivityRepository;
use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Http\Repositories\CustomFieldRepository;
use App\Http\Repositories\LeadAttachmentRepository;
use App\Http\Repositories\LeadRepository;
use App\Http\Resource\LeadResource;
use App\Models\Activity;
use App\Models\CustomFieldValues;
use App\Models\EntityLog;
use App\Models\Lead;
use App\Models\LeadAttachments;
use App\Models\User;
use App\Traits\CustomFieldRequiredFieldValidationTrait;
use App\Traits\SyncDataFromApolloTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use Throwable;

class LeadService extends BaseService
{
    use CustomFieldRequiredFieldValidationTrait;

    use SyncDataFromApolloTrait;

    private LeadAttachmentRepository $leadAttachmentRepository;

    public const APOLLO_TO_CUSTOM_FIELD_MAPPING = [
        'first_name' => 'first-name',
        'last_name' => 'last-name',
        'linkedin_url' => 'linkedin_url',
        'title' => 'apollo_title',
        'photo_url' => 'avatar',
        'email' => 'email',
    ];

    public function __construct(
        LeadRepository $leadRepository,
        LeadAttachmentRepository $leadAttachmentRepository,
        private ApolloIoService $apolloIoService,
        private CustomFieldValueRepository $customFieldValueRepository,
        private CustomFieldRepository $customFieldRepository,
        private ActivityRepository $activityRepository,
    ) {
        $this->repository = $leadRepository;
        $this->leadAttachmentRepository = $leadAttachmentRepository;
    }

    public function resource(): string
    {
        return LeadResource::class;
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
            $this->repository->getAllWithCustomFields($params, $user),
        );
    }

    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return JsonResource
     * @throws ModelCreateErrorException
     */
    public function store(array $data, Authenticatable|User $user): JsonResource
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Lead::class;

        if (!isset($data['customFields']['lead-title'])) {
            $data['customFields']['lead-title'] = 'New Lead';
        }

        if (!isset($data['customFields']['lead-owner'])) {
            $user = User::query()->where('email', User::AJAY_EMAIL)->first();
            if ($user) {
                $data['customFields']['lead-owner'] = $user->getKey();
            }
        }

        return parent::store($data, $user);
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
        $data['entity_type'] = Lead::class;
        $data['updated_by'] = $user->getKey();
        if (isset($data['avatarFile'])) {
            try {
                $savePath = '/lead/' . $model->getKey() . '/avatar';
                $savedFile = StorageHelper::storeFile($data['avatarFile'], $savePath);
                $data['avatar'] = $savedFile;
            } catch (Throwable $e) {
                throw new CustomErrorException($e->getMessage(), 422);
            }
        }

        return $data;
    }


    /**
     * @param Model $model
     * @param Authenticatable $user
     * @return bool
     * @throws ModelDeleteErrorException
     */
    public function delete(Model $model, Authenticatable $user): bool
    {
        parent::delete($model, $user);
        ModelDeleted::dispatch($model, $user);

        return true;
    }

    /**
     * @param array $params
     * @param User|Authenticatable $user
     * @return void
     * @throws ModelDeleteErrorException
     */
    public function bulkDelete(array $params, User|Authenticatable $user): void
    {
        $recordsForDelete = [];
        $idList = array_filter(explode(',', $params['ids']));

        foreach ($idList as $id) {
            /** @var Lead $lead */
            $lead = $this->repository->findById($id);
            $recordsForDelete[] = $lead;
        }

        $result = true;

        foreach ($recordsForDelete as $instance) {
            $result &= $this->delete($instance, $user);
        }

        if (!$result) {
            throw new ModelDeleteErrorException();
        }
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            [
                'tag',
                'attachments',
                'customFields',
                'activity',
                'activity.reminders',
                'activity.relatedUser',
                'internalNote',
            ],
        );

        return parent::show($model, $resource);
    }

    /**
     * @param array $data
     * @param Lead $lead
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Lead $lead, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->leadAttachmentRepository->create([
                'lead_id' => $lead->getKey(),
                'attachment_link' => $data['link'],
                'name' => $data['name'] ?? '',
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/lead/' . $lead->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->leadAttachmentRepository->create([
                        'lead_id' => $lead->getKey(),
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
     * @param Lead $lead
     * @param LeadAttachments $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Lead $lead,
        LeadAttachments $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->leadAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'name' => $data['name'] ?? '',
                'attachment_file' => null,
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/lead/' . $lead->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->leadAttachmentRepository->update($attachment, [
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

    public function deleteAttachment(LeadAttachments $attachment): void
    {
        $this->leadAttachmentRepository->delete($attachment);
    }

    /**
     * @param Lead $lead
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(
        Lead $lead,
        User|Authenticatable $user,
    ): void {
        $this->syncPeopleDataFromApollo($lead, $user);
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
        /** @var Lead $model */
        if (!empty($data['internalNotes'])) {
            if (!isset($data['customFields']['lead-owner'])) {
                throw new CustomErrorException('lead-owner custom field required', 422);
            }
            $activityNoteData = [
                'related_to' => $data['customFields']['lead-owner'],
                'subject' => 'New Lead Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Lead::class,
                'related_to_id' => $model->getKey(),
                'activity_status' => 'Not started',
                'activity_type' => Activity::ACTIVITY_TYPE_INTERNAL_NOTE,
                'created_by' => $model->created_by,
            ];
            $activity = $this->activityRepository->create($activityNoteData);

            $changedEntityLog = [
                'entity' => Lead::class,
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
            if (isset($data['customFields']['lead-owner'])) {
                $leadOwner = $data['customFields']['lead-owner'];
            } else {
                /** @var  CustomFieldValues $leadOwnerCustomFieldValue */
                $leadOwnerCustomFieldValue = CustomFieldValues::query()->where('entity_id', $model->getKey())
                    ->where('entity', Lead::class)->whereHas('customField', function ($query) {
                        $query->where('entity_type', Lead::class)->where('code', 'lead-owner');
                    })->first();
                $leadOwner = $leadOwnerCustomFieldValue->integer_value;
            }
            /** @var Lead $model */
            $activityNoteData = [
                'related_to' => $leadOwner,
                'subject' => 'New Lead Notes',
                'description' => $data['internalNotes'],
                'due_date' => date('Y-m-d'),
                'related_to_entity' => Lead::class,
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
