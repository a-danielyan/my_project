<?php

namespace App\Http\Services;

use App\Helpers\GeographicHelper;
use App\Http\Resource\CityResource;
use App\Http\Resource\LocationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommonCityService extends BaseService
{
    public function resource(): string
    {
        return CityResource::class;
    }

    /**
     * This function used by controller for autocomplete locations
     * @param string $search
     * @return ResourceCollection
     */
    public function findLocation(string $search): ResourceCollection
    {
        $resource = LocationResource::class;

        return $resource::collection(GeographicHelper::geographicData($search, [], false) ?? []);
    }
}
