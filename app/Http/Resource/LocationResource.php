<?php

namespace App\Http\Resource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'house' => $this['house'] ?? null,
            'street' => $this['street'] ?? null,
            'area' => $this['area'] ?? null,
            'name' => $this['name'] ?? null,
            'state' => $this['state'] ?? null,
            'country' => $this['country'] ?? null,
            'zipcode' => $this['zipcode'] ?? null,
            'longitude' => $this['longitude'] ?? null,
            'latitude' => $this['latitude'] ?? null,
            'formattedAddress' => $this['formatted_address'] ?? null,
            'placeId' => $this['place_id'] ?? null,
            'timezone' => $this['timezone'],
        ];
    }
}
