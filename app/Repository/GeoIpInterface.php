<?php

declare(strict_types = 1);

namespace App\Repository;

interface GeoIpInterface
{
    public function search(string $ip): GeoIpOutput;
}
