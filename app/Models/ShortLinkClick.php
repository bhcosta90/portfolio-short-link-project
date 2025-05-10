<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ShortLinkClick extends Model
{
    protected $fillable = [
        'ip_address',
        'endpoint',
    ];
}
