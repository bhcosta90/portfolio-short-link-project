<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Model;

final class GeoIp extends Model
{
    use AsHashed;
    protected $fillable = [
        'is_success',
        'ip_address',
        'country',
        'country_code',
        'region',
        'region_name',
        'city',
        'zip',
        'lat',
        'lon',
        'timezone',
        'isp',
        'org',
        'as',
    ];

    protected function casts(): array
    {
        return [
            'is_success' => 'boolean',
        ];
    }
}
