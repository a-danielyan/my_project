<?php

namespace App\Http\Resource;

use App\Models\TusFileData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DeviceLocationViewResource
 * @package App\Http\Resources\Common\Device
 * @mixin TusFileData
 */
class UserProfileDetailsCustomResource extends JsonResource
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
            'filename' => $this->name,
            'isCustom' => true
        ];
    }
}
