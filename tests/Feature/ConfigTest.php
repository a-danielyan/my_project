<?php

use App\Helpers\GeographicHelper;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ConfigTest extends TestCase
{

    public function test_get_city_response(): void
    {
        $location = 'New York';

        $locationHash = self::geocodeHash($location . '$', false);
        Cache::remember($locationHash, 9000, function () {
            return [];
        });
        $response = $this->get(self::FIND_LOCATION_ROUTE . '?search=New York');
        $response->assertStatus(200);
    }


    private static function geocodeHash(string $location, bool $firstResultOnly = true): string
    {
        return GeographicHelper::GEOCODE_KEY_PREFIX . ($firstResultOnly ? '' : 'MULTI') . sha1($location);
    }
}
