<?php

namespace App\Http\Resource;

use App\Models\AccountTraining;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AccountTraining
 */
class AccountTrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trainingDate' => $this->training_date,
            'trainedBy' => $this->trainedBy,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'note' => $this->note,
            'activity' => new ActivityMinimalResource($this->activity),
        ];
    }
}
