<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\RegisterClickShortLinkEvent;
use App\Models\GeoIp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Throwable;

final class GeoIpListener implements ShouldQueue
{
    use InteractsWithQueue;
    //
    //    public int $tries   = 3;
    //    public int $backoff = 10;
    public int $timeout = 3; // Timeout em segundos

    public function __construct()
    {
        //
    }

    public function handle(RegisterClickShortLinkEvent $event): void
    {
        $ip = $event->ipAddress;

        if ('127.0.0.1' === $ip && app()->isLocal() && filled($configGeoIp = config('geo-ip.ip'))) {
            $ip = $configGeoIp;
        }

        $geoIp = GeoIp::query()->whereIpAddress($ip)
            ->whereIsSuccess(true)
            ->exists();

        if ($geoIp) {
            return;
        }

        try {
            $response = Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}");

            if ($response->successful() && 'success' === $response['status']) {
                $data = [
                    'is_success'   => true,
                    'country'      => $response['country'],
                    'region'       => $response['region'],
                    'region_name'  => $response['regionName'],
                    'country_code' => $response['countryCode'],
                    'city'         => $response['city'],
                    'zip'          => $response['zip'],
                    'lat'          => $response['lat'],
                    'lon'          => $response['lon'],
                    'timezone'     => $response['timezone'],
                    'isp'          => $response['isp'],
                    'org'          => $response['org'],
                    'as'           => $response['as'],
                ];
            }
        } catch (Throwable) {
            $data = [
                'is_success' => false,
                'ip_address' => $ip,
            ];

        }

        GeoIp::updateOrCreate([
            'ip_address' => $ip,
        ], $data);
    }
}
