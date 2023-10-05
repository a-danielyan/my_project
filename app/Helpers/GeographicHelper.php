<?php

namespace App\Helpers;

use App\Helpers\CommonHelper as Help;
use App\Helpers\ThirdPartyApiHelper as ApiHelper;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Class GeographicHelper
 * @package App\Helpers
 */
final class GeographicHelper
{
    public const GEOCODE_KEY_PREFIX = 'geoCoding';

    /**
     * Please use CommonCityService instead of direct call geographicData
     * TODO rewrite: should return City collection
     * @param string $location
     * @param array $types
     * @param bool $firstResultOnly
     * @param string $language
     * @return array|null
     */
    public static function geographicData(
        string $location,
        array $types = [],
        bool $firstResultOnly = true,
        string $language = '',
    ): ?array {
        $locationHash = self::geocodeHash($location . '$' . $language, $firstResultOnly);
        $ttl = config('services.google.GEO_CODE_TTL', 300);

        return Cache::remember($locationHash, $ttl, function () use ($location, $types, $firstResultOnly, $language) {
            return self::geographicDataFromAPI($location, $types, $firstResultOnly, $language);
        });
    }


    /**
     * @param string $location
     * @param array $types
     * @param bool $firstResultOnly
     * @param string $language
     * @return array|null
     * @throws GuzzleException
     */
    private static function geographicDataFromAPI(
        string $location,
        array $types = [],
        bool $firstResultOnly = true,
        string $language = '',
    ): ?array {
        $result = Help::fileGetContentsGuzzle(ApiHelper::getGoogleAPIGeoCodeByAddress($location, $types, $language));
        if (empty($result)) {
            return null;
        }

        $geoCodeData = json_decode($result, true);
        if (empty($geoCodeData['results'])) {
            return null;
        }

        $result = array();
        foreach ($geoCodeData['results'] as $geoCodeResult) {
            if (empty($geoCodeResult['address_components'])) {
                continue;
            }
            $timeZoneData = Help::fileGetContentsGuzzle(
                ApiHelper::getGoogleTimezoneByLatLon(
                    $geoCodeResult['geometry']['location']['lat'],
                    $geoCodeResult['geometry']['location']['lng'],
                ),
            );
            $geoCodeResult['timezone'] = [];
            try {
                $geoCodeResult['timezone'] = json_decode($timeZoneData, true);
            } catch (Throwable) {
            }

            if ($firstResultOnly) {
                return self::processCityInfo($geoCodeResult, $language);
            }

            $result[] = self::processCityInfo($geoCodeResult, $language);
        }

        return $result;
    }

    /**
     * @param array $data
     * @param string $language
     * @return array
     */
    public static function processCityInfo(array $data, string $language = ''): array
    {
        $result = [];

        $map = [
            'street_number' => 'house',
            'route' => 'street',
            'sublocality_level_1' => 'name', //fix search `145`
            'locality' => 'name',
            'sublocality' => 'area',
            'administrative_area_level_2' => 'state',
            'administrative_area_level_1' => 'state',
            'postal_code' => 'zipcode',
            'country' => 'country',
        ];

        foreach ($data['address_components'] as $item) {
            foreach ($item['types'] as $type) {
                if (isset($map[$type])) {
                    $result[$map[$type]] = ['longName' => $item['long_name'], 'shortName' => $item['short_name']];
                }
            }
        }

        if (empty($result['name'])) {
            $result['name'] = $data['address_components'][0]['long_name'];
        }

        $result['latitude'] = (float)$data['geometry']['location']['lat'];
        $result['longitude'] = (float)$data['geometry']['location']['lng'];

        $result['formatted_address'] = $data['formatted_address'];
        $result['place_id'] = $data['place_id'];
        $result['timezone'] = $data['timezone'];
        if (!empty($language)) {
            $result['language'] = $language;
        }

        return $result;
    }

    private static function geocodeHash(string $location, bool $firstResultOnly = true): string
    {
        return self::GEOCODE_KEY_PREFIX . ($firstResultOnly ? '' : 'MULTI') . sha1($location);
    }
}
