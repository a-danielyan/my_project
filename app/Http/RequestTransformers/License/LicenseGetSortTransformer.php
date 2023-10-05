<?php

namespace App\Http\RequestTransformers\License;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class LicenseGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return array_merge(parent::getMap(), [
            'licenseType' => 'license_type',
            'licenseDurationInMonth' => 'license_duration_in_month',
        ]);
    }
}
