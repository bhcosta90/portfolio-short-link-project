<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class ShortLinkClick extends Model
{
    protected $fillable = [
        'ip_address',
        'endpoint',
    ];

    public function geoIp(): HasOne
    {
        return $this->hasOne(GeoIp::class, 'ip_address', 'ip_address');
    }
}
