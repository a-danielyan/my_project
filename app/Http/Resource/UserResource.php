<?php

namespace App\Http\Resource;

use App\Models\User;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;

/**
 * Class UserResource
 * @mixin User
 */
class UserResource extends BaseUserResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $resultArray = array_merge_recursive(
            parent::toArray($request),
            [
                'lastLogin' => $this->lastLogin?->activity_time,
                'lastAuthActivity' => $this->lastAuthActivity,
                'file' => ProfileResource::collection($this->storedFiles),
                'dashboardBlocks' => $this->dashboard_blocks,
            ],
        );

        return $this->filterResourceByColumns($request, $resultArray);
    }
}
