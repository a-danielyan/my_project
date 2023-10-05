<?php

namespace App\Http\Resource;

use App\Helpers\StorageHelper;
use App\Models\TusFileData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProfileResource
 * @mixin TusFileData
 * @package App\Http\Resources\Common\Client
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->media->profile_uri,
            'type' => $this->type,
            'preview' => StorageHelper::getStorageUrl(
                $this->media::PROFILE_URI . $this->media->profile_uri,
                null,
                true
            ),
            'data' => [
                'size' => $this->size,
            ],
        ];
    }
}
