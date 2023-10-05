<?php

namespace App\Helpers;

class ThirdPartyApiHelper
{
    public const GOOGLE_GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json?';
    public const GOOGLE_TIMEZONE_URL = 'https://maps.googleapis.com/maps/api/timezone/json?';

    /**
     * @param string $location
     * https://developers.google.com/maps/documentation/places/web-service/supported_types
     * @param array $types
     * @param string $language
     * @return string
     */
    public static function getGoogleAPIGeoCodeByAddress(
        string $location,
        array $types = [],
        string $language = '',
    ): string {
        $params = [
            'address' => $location,
            'key' => config('services.google.GOOGLE_MAP_GEOCODING_KEY'),
        ];
        if (!empty($language)) {
            $params['language'] = $language;
        }

        if (!empty($types)) {
            $params['types'] = $types;
        }

        return self::GOOGLE_GEOCODE_URL . http_build_query($params);
    }

    public static function getGoogleTimezoneByLatLon(
        string $lat,
        string $lon,
    ): string {
        $params = [
            'location' => $lat . ',' . $lon,
            'key' => config('services.google.GOOGLE_MAP_GEOCODING_KEY'),
            'timestamp' => time(),
        ];

        return self::GOOGLE_TIMEZONE_URL . http_build_query($params);
    }
}
