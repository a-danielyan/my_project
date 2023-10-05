<?php

namespace App\Http\Services;

use App\Events\ModelChanged;
use App\Http\Repositories\ActivityRepository;
use App\Http\Resource\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityReminder;
use App\Models\EntityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityService extends BaseService
{
    public function __construct(
        ActivityRepository $activityRepository,
    ) {
        $this->repository = $activityRepository;
    }

    public function resource(): string
    {
        return ActivityResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params, ['tag']));
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Activity::class;
        if (!isset($data['ended_at'])) {
            $data['ended_at'] = $data['started_at'] ?? null;
        }
        $data['related_to_entity'] = 'App\Models\\' . $data['related_to_entity'];

        return $data;
    }

    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Activity $model */
        if (isset($data['reminders'])) {
            $this->updateReminders($data['reminders'], $model);
        }

        $changedEntityLog = [
            'entity' => $model->related_to_entity,
            'entity_id' => $model->related_to_id,
            'field_id' => null,
            'previous_value' => null,
            'new_value' => 'Activity created',
            'updated_by' => $user->getKey(),
            'update_id' => time(),
            'created_at' => now(),
            'log_type' => EntityLog::NOTE_LOG_TYPE,
            'activity_id' => $model->getKey(),
        ];
        ModelChanged::dispatch($changedEntityLog);

        parent::afterStore($model, $data, $user);
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();
        $data['entity_type'] = Activity::class;
        $data['related_to_entity'] = 'App\Models\\' . $data['related_to_entity'];

        return $data;
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Activity $model */
        if (isset($data['reminders'])) {
            $this->updateReminders($data['reminders'], $model);
        }
    }


    private function updateReminders(array $reminders, Activity $model): void
    {
        ActivityReminder::query()->where('activity_id', $model->getKey())->delete();
        if (!empty($reminders)) {
            foreach ($reminders as $reminder) {
                ActivityReminder::query()->insert([
                    'activity_id' => $model->getKey(),
                    'reminder_type' => $reminder['reminderType'],
                    'reminder_time' => $reminder['reminderTime'],
                    'reminder_unit' => $reminder['reminderUnit'],
                ]);
            }
        }
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load('tag');

        return parent::show($model, $resource);
    }
}
