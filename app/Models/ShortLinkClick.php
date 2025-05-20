<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShortLinkClick extends Model
{
    use HasFactory;

    public function geoIp(): BelongsTo
    {
        return $this->belongsTo(GeoIp::class);
    }
}
