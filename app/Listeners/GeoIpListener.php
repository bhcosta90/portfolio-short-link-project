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

    public int $tries   = 3;
    public int $backoff = 10;
    public int $timeout = 3;

    public function __construct()
    {
        //
    }

    public function handle(CreatedClickShortLinkEvent $event): void
    {
        $geoIp = $this->getRecentGeoIp($event->ipAddress);

        $shortLink = ShortLinkClick::find($event->id);

        if ($this->shouldAttachExistingGeoIp($geoIp)) {
            $shortLink->shortLinkGeoIp()->attach($geoIp);

            return;
        }

        $searchGeoIp = GeoIpFacade::search($event->ipAddress);

        if ($this->shouldCreateNewGeoIp($geoIp, $searchGeoIp)) {
            $geoIp = $this->createGeoIp($event->ipAddress, $searchGeoIp);
        }

        $shortLink->shortLinkGeoIp()->attach($geoIp);
    }

    private function getRecentGeoIp(string $ip): ?GeoIp
    {
        return GeoIp::query()
            ->whereIpAddress($ip)
            ->whereIsSuccess(true)
            ->orderByDesc('id')
            ->first();
    }

    private function shouldAttachExistingGeoIp(?GeoIp $geoIp): bool
    {
        return $geoIp && $geoIp->created_at->diffInDays(now()) < 1;
    }

    private function shouldCreateNewGeoIp(?GeoIp $geoIp, GeoIpOutput $searchGeoIp): bool
    {
        return blank($geoIp?->id)
            || $geoIp->lat !== $searchGeoIp->lat
            || $geoIp->lon !== $searchGeoIp->lon;
    }

    private function createGeoIp(string $ip, GeoIpOutput $searchGeoIp): GeoIp
    {
        return GeoIp::create([
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
}
