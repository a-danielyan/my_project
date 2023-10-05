<?php

namespace App\Http\Resource;

use App\Models\Opportunity;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

/**
 * @mixin Opportunity
 */
class OpportunityMinimalResource extends BaseResourceWithCustomField
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
        $customFieldValues = $this->getCustomFieldValues();

        return [
            'id' => $this->id,
            'customFields' => $this->getCustomFields($customFieldValues),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'status' => $this->getStatus(),
            'stage' => $this->stage,
            'opportunityName' => $this->opportunity_name,
            'projectType' => $this->project_type,
            'expectingClosingDate' => $this->expecting_closing_date,
            'expectedRevenue' => $this->expected_revenue,
            'closedAt' => $this->closed_at,
            'totalDuration' => $this->getTotalDuration(),
            'internalNote' => new  ActivityMinimalResource($this->whenLoaded('internalNote')),
        ];
    }

    private function getTotalDuration(): ?string
    {
        if (empty($this->closed_at)) {
            return null;
        }

        return $this->created_at->diffForHumans(
            $this->closed_at,
            CarbonInterface::DIFF_ABSOLUTE,
            short: true,
            parts: 2,
        );
    }
}
