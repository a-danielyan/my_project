<?php

namespace App\Http\Resource;

use App\Models\AccountDemo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AccountDemo
 */
class AccountDemoResource extends JsonResource
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
            'demoDate' => $this->demo_date,
            'trainedBy' => $this->trainedBy,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'note' => $this->note,
            'activity' => new ActivityMinimalResource($this->activity),
        ];
    }
}
