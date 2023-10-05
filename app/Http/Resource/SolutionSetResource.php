<?php

namespace App\Http\Resource;

use App\Models\SolutionSet;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SolutionSet
 */
class SolutionSetResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'items' => SolutionSetItemResource::collection($this->items),
        ];
    }
}
