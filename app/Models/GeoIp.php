<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Model;

final class GeoIp extends Model
{
    use AsHashed;

    protected function casts(): array
    {
        return [
            'is_success'  => 'boolean',
            'qtd_retries' => 'integer',
        ];
    }
}
