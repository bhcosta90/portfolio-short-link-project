<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\CreatedClickShortLinkEvent;
use App\Models\GeoIp;
use App\Models\ShortLinkClick;
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

    public function handle(CreatedClickShortLinkEvent $event): void
    {
        $ip = $event->ipAddress;

        $geoIp = GeoIp::query()->whereIpAddress($ip)
            ->whereIsSuccess(true)
            ->orderBy('id', 'desc')
            ->limit(1)
            ->first();

        $shortLink = ShortLinkClick::find($event->id);

        if ($geoIp && $geoIp->created_at->diffInDays(now()) < 1) {
            $shortLink->shortLinkGeoIp()->attach($geoIp);

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

        if (blank($geoIp?->id)
            || $geoIp->lat !== $data['lat']
            || $geoIp->lon !== $data['lon']
        ) {
            $geoIp = GeoIp::create($data);
        }

        $shortLink->shortLinkGeoIp()->attach($geoIp);
    }
}
