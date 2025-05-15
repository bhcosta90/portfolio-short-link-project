<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShortLinkClick extends Model
{
    use AsHashed;

    public function geoIp(): BelongsTo
    {
        return $this->belongsTo(GeoIp::class);
    }
}
