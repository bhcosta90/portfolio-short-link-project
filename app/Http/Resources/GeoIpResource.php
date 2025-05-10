<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\GeoIp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin GeoIp */
final class GeoIpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'ip_address'   => $this->ip_address,
            'is_success'   => $this->is_success,
            'country'      => $this->country,
            'country_code' => $this->country_code,
            'region'       => $this->region,
            'region_name'  => $this->region_name,
            'city'         => $this->city,
            'zip'          => $this->zip,
            'lat'          => $this->lat,
            'lon'          => $this->lon,
            'timezone'     => $this->timezone,
            'isp'          => $this->isp,
            'org'          => $this->org,
            'as'           => $this->as,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
