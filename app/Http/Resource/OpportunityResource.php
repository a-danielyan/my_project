<?php

namespace App\Http\Resource;

use App\Models\Opportunity;
use App\Models\Stage;
use App\Models\User;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Throwable;

/**
 * @mixin Opportunity
 */
class OpportunityResource extends OpportunityMinimalResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;
    use GetRecordStatusTrait;

    protected static array $allStages = [];

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($this->resource == null) {
            return [];
        }

        return array_merge(parent::toArray($request), [
            'stageLog' => $this->showStageLog(),
            'estimates' => EstimateResource::collection($this->whenLoaded('estimates')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'account' => new  AccountResource($this->account, customFieldList: ['account-name', 'sales-tax']),
        ]);
    }

    private function showStageLog(): array
    {
        if (!$this->relationLoaded('stageLog')) {
            return [];
        }

        if (empty(self::$allStages)) {
            self::$allStages = Stage::query()->where('status', User::STATUS_ACTIVE)
                ->orderBy('sort_order')->select(['id', 'name', 'sort_order'])->get()->keyBy('id')->toArray();
        }

        $stagesDuration = [];
        $allStages = $this->stageLog;
        $previousStage = '';
        foreach ($allStages as $stage) {
            if (!isset(self::$allStages[$stage->stage_id])) {
                continue;
            }
            if (!isset($stagesDuration[$stage->stage_id])) {
                $stagesDuration[$stage->stage_id] = [
                    'duration' => 0,
                    'sort_order' => self::$allStages[$stage->stage_id]['sort_order'],
                ];
            }

            if (!empty($previousStage)) {
                $duration = $stage->created_at->diffInSeconds(
                    $previousStage->created_at,
                );
                $stagesDuration[$previousStage->stage_id]['duration'] += $duration;
            }

            $previousStage = $stage;
        }
        if ($previousStage !== '') {
            $durationForLastStage = $previousStage->created_at?->diffInSeconds(now());
            $stagesDuration[$previousStage->stage_id]['duration'] += $durationForLastStage;
        }

        $lastStage = $previousStage;

        $result = [];
        foreach (self::$allStages as $stage) {
            $duration = $stagesDuration[$stage['id']]['duration'] ?? 0;
            if ($lastStage !== '') {
                if ($stage['sort_order'] === self::$allStages[$lastStage->stage_id]['sort_order']) {
                    //if we match  last stage - then we add all further  stages duration to  last stage
                    foreach ($stagesDuration as $value) {
                        if ($value['sort_order'] > self::$allStages[$lastStage->stage_id]['sort_order']) {
                            $duration += $value['duration'];
                        }
                    }
                }

                if ($duration > 0) {
                    try {
                        $duration = CarbonInterval::seconds($duration)->cascade()->forHumans(short: true, parts: 2);
                    } catch (Throwable) {
                        $duration = 0;
                    }
                }
            }
            $result[] = ['stageId' => $stage['id'], 'stage' => $stage['name'], 'duration' => $duration];
        }

        return $result;
    }
}
