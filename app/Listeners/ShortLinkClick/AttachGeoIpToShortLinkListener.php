<?php

declare(strict_types = 1);

namespace App\Listeners\ShortLinkClick;

use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Facades\GeoIpFacade;
use App\Models\GeoIp;
use App\Models\ShortLinkClick;
use App\Repositories\GeoIp\SearchOutput;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

final class AttachGeoIpToShortLinkListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries   = 3;
    public int $backoff = 10;
    public int $timeout = 3;

    public function __construct()
    {
        //
    }

    public function handle(ShortLinkClickRecordedEvent $event): void
    {
        $geoIp = $this->getRecentGeoIp($event->ipAddress);

        $shortLink = ShortLinkClick::find($event->id);

        if ($this->shouldAttachExistingGeoIp($geoIp)) {
            $shortLink->geo_ip_id = $geoIp?->id;
            $shortLink->save();

            return;
        }

        $searchGeoIp = GeoIpFacade::search($event->ipAddress);

        $geoIp = $this->shouldCreateNewGeoIp($geoIp, $searchGeoIp)
            ? $this->createGeoIp($event->ipAddress, $searchGeoIp)
            : tap($geoIp)->touch();

        $shortLink->geo_ip_id = $geoIp->id;
        $shortLink->save();
    }

    private function getRecentGeoIp(string $ip): ?GeoIp
    {
        return GeoIp::query()
            ->whereIpAddress($ip)
            ->orderByDesc('id')
            ->first();
    }

    private function shouldAttachExistingGeoIp(?GeoIp $geoIp): bool
    {
        return $geoIp && $geoIp->updated_at->diffInDays(now()) < 1;
    }

    private function shouldCreateNewGeoIp(?GeoIp $geoIp, SearchOutput $searchGeoIp): bool
    {
        return blank($geoIp?->id)
            || $geoIp?->lat !== $searchGeoIp->lat
            || $geoIp?->lon !== $searchGeoIp->lon;
    }

    private function createGeoIp(string $ip, SearchOutput $searchGeoIp): GeoIp
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
