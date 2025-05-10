<?php

declare(strict_types = 1);

namespace App\Repository\GeoIp;

final class SearchOutput
{
    public function __construct(
        public ?string $country = null,
        public ?string $region = null,
        public ?string $regionName = null,
        public ?string $countryCode = null,
        public ?string $city = null,
        public ?string $zip = null,
        public ?float $lat = null,
        public ?float $lon = null,
        public ?string $timezone = null,
        public ?string $isp = null,
        public ?string $org = null,
        public ?string $as = null,
        public bool $isSuccess = false,
    ) {
    }
}
