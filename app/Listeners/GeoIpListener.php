<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\RegisterClickShortLinkEvent;
use App\Models\GeoIp;
use App\Models\ShortLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Throwable;

final class GeoIpListener implements ShouldQueue
{
    use InteractsWithQueue;
    //
    public int $tries   = 3;
    public int $backoff = 10;
    public int $timeout = 3; // Timeout em segundos

    public function __construct()
    {
        //
    }

    public function handle(RegisterClickShortLinkEvent $event): void
    {
        $ip = $event->ipAddress;

        $geoIp = GeoIp::query()->whereIpAddress($ip)
            ->whereIsSuccess(true)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($geoIp) {
            return;
        }

        $data = [
            'ip_address' => $ip,
        ];

        try {
            $response = Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}");

            if ($response->successful() && 'success' === $response['status']) {
                $data += [
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
            $data += [
                'is_success' => false,
            ];

        }

        $geoIp     = GeoIp::create($data);
        $shortLink = ShortLink::find($event->id);
        $shortLink->shortLinkGeoIp()->attach($geoIp);
    }
}
