<?php

declare(strict_types = 1);

namespace App\Repositories\GeoIp;

use Illuminate\Support\Facades\Http;
use Throwable;

final readonly class IpApiGeoIp implements GeoIpInterface
{
    public function search(string $ip): SearchOutput
    {
        try {
            $response = Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}");

            if ($response->successful() && 'success' === $response['status']) {
                return new SearchOutput(
                    country: $response['country'],
                    region: $response['region'],
                    regionName: $response['regionName'],
                    countryCode: $response['countryCode'],
                    city: $response['city'],
                    zip: $response['zip'],
                    lat: $response['lat'],
                    lon: $response['lon'],
                    timezone: $response['timezone'],
                    isp: $response['isp'],
                    org: $response['org'],
                    as: $response['as'],
                    isSuccess: true
                );
            }
        } catch (Throwable) {
            return new SearchOutput();
        }
    }
}
