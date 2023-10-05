<?php

namespace App\Http\Resource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CityResource extends JsonResource
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
            'id' => $this->id,
            'area' => $this->area,
            'name' => $this->name,
            'state' => $this->state,
            'country' => $this->country,
            'zipcode' => $this->zipcode,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'formattedAddress' => $this->formatted_address,
            'placeId' => $this->place_id,
        ];
    }
}
