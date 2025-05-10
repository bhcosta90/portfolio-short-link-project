<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class ShortLinkClick extends Model
{
    use AsHashed;
    protected $fillable = [
        'ip_address',
        'endpoint',
    ];

    public function shortLinkGeoIp(): BelongsToMany
    {
        return $this->belongsToMany(GeoIp::class);
    }
}
