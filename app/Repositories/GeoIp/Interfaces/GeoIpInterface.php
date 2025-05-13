<?php

declare(strict_types = 1);

namespace App\Repositories\GeoIp\Interfaces;

use App\Repositories\GeoIp\Data\SearchOutput;

interface GeoIpInterface
{
    public function search(string $ip): SearchOutput;
}
