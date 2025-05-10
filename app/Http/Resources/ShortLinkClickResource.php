<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\ShortLinkClick;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ShortLinkClick */
final class ShortLinkClickResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'short_link_id' => $this->short_link_id,
            'endpoint'      => $this->endpoint,
            'ip_address'    => $this->ip_address,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'geo_ip'        => new GeoIpResource($this->whenLoaded('geoIp')),
        ];
    }
}
