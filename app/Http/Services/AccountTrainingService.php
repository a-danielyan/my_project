<?php

namespace App\Http\Services;

use App\Events\ModelChanged;
use App\Http\Repositories\AccountTrainingRepository;
use App\Http\Repositories\ActivityRepository;
use App\Http\Resource\AccountTrainingResource;
use App\Models\Account;
use App\Models\AccountTraining;
use App\Models\Activity;
use App\Models\EntityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountTrainingService extends BaseService
{
    public function __construct(
        AccountTrainingRepository $accountTrainingRepository,
        private ActivityRepository $activityRepository,
    ) {
        $this->repository = $accountTrainingRepository;
    }

    public function resource(): string
    {
        return AccountTrainingResource::class;
    }

    /**
     * @param Account $account
     * @return AnonymousResourceCollection
     */
    public function getAll(Account $account): AnonymousResourceCollection
    {
        return AccountTrainingResource::collection($this->repository->getAllForAccount($account));
    }


    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['activity_id'] = null;
        if (!empty($data['subject'])) {
            $activityData = [
                'related_to' => $data['related_to'],
                'activity_type' => Activity::ACTIVITY_TYPE_TASK,
                'activity_status' => Activity::ACTIVITY_STATUS_NOT_STARTED,
                'due_date' => $data['due_date'],
                'subject' => $data['subject'],
                'related_to_entity' => Account::class,
                'related_to_id' => $data['account_id'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? Activity::PRIORITY_NORMAL_STATUS,
                'created_by' => $user->getKey(),
            ];
            $activity = $this->activityRepository->create($activityData);
            $data['activity_id'] = $activity->getKey();

            $changedEntityLog = [
                'entity' => Account::class,
                'entity_id' => $data['account_id'],
                'field_id' => null,
                'previous_value' => null,
                'new_value' => 'Activity created',
                'updated_by' => $user->getKey(),
                'update_id' => time(),
                'created_at' => now(),
                'log_type' => EntityLog::NOTE_LOG_TYPE,
                'activity_id' => $data['activity_id'],
            ];
            ModelChanged::dispatch($changedEntityLog);
        }
        $data['created_by'] = $user->getKey();

        return $data;
    }


    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        /** @var AccountTraining $model */
        $activity = $model->activity;
        $data['updated_by'] = $user->getKey();

        if ($activity) {
            $this->activityRepository->update($activity, $data);
        } else {
            if (!empty($data['subject'])) {
                $activityData = [
                    'related_to' => $data['related_to'],
                    'activity_type' => Activity::ACTIVITY_TYPE_TASK,
                    'activity_status' => Activity::ACTIVITY_STATUS_NOT_STARTED,
                    'due_date' => $data['due_date'],
                    'subject' => $data['subject'],
                    'related_to_entity' => Account::class,
                    'related_to_id' => $model->account_id,
                    'description' => $data['description'],
                    'priority' => $data['priority'] ?? Activity::PRIORITY_NORMAL_STATUS,
                    'created_by' => $user->getKey(),
                ];
                $activity = $this->activityRepository->create($activityData);
                $data['activity_id'] = $activity->getKey();

                $changedEntityLog = [
                    'entity' => Account::class,
                    'entity_id' => $model->account_id,
                    'field_id' => null,
                    'previous_value' => null,
                    'new_value' => 'Activity created',
                    'updated_by' => $user->getKey(),
                    'update_id' => time(),
                    'created_at' => now(),
                    'log_type' => EntityLog::NOTE_LOG_TYPE,
                    'activity_id' => $data['activity_id'],
                ];
                ModelChanged::dispatch($changedEntityLog);
            }
        }

        return $data;
    }
}
