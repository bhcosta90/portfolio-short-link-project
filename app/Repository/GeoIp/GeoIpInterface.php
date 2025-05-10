<?php

declare(strict_types = 1);

namespace App\Repository\GeoIp;

interface GeoIpInterface
{
    public function search(string $ip): SearchOutput;
}
