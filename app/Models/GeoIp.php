<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class GeoIp extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_success'  => 'boolean',
            'qtd_retries' => 'integer',
        ];
    }
}
