<?php

declare(strict_types = 1);

namespace App\Facades;

use App\Repositories\GeoIp\IpApiGeoIp;
use Illuminate\Support\Facades\Facade;

/**
 * @see IpApiGeoIp
 */
final class GeoIpFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return IpApiGeoIp::class;
    }
}
