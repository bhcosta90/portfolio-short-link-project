<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\CreatedClickShortLinkEvent;
use App\Facades\GeoIpFacade;
use App\Models\GeoIp;
use App\Models\ShortLinkClick;
use App\Repository\GeoIpOutput;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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

        /** @var GeoIpOutput $searchGeoIp */
        $searchGeoIp = GeoIpFacade::search($ip);

        if (blank($geoIp?->id)
            || $geoIp->lat !== $searchGeoIp->lat
            || $geoIp->lon !== $searchGeoIp->lon
        ) {
            $geoIp = GeoIp::create([
                'ip_address'   => $ip,
                'country'      => $searchGeoIp->country,
                'region'       => $searchGeoIp->region,
                'region_name'  => $searchGeoIp->regionName,
                'country_code' => $searchGeoIp->countryCode,
                'city'         => $searchGeoIp->city,
                'zip'          => $searchGeoIp->zip,
                'lat'          => $searchGeoIp->lat,
                'lon'          => $searchGeoIp->lon,
                'timezone'     => $searchGeoIp->timezone,
                'isp'          => $searchGeoIp->isp,
                'org'          => $searchGeoIp->org,
                'as'           => $searchGeoIp->as,
                'is_success'   => $searchGeoIp->isSuccess,
            ]);
        }

        $shortLink->shortLinkGeoIp()->attach($geoIp);
    }
}
