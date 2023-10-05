<?php

namespace App\Http\RequestTransformers\License;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LicenseStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'name' => 'name',
                'licenseDurationInMonth' => 'license_duration_in_month',
                'licenseType' => 'license_type',
                'tag' => 'tag',
                'status' => 'status',
            ];
    }
}
