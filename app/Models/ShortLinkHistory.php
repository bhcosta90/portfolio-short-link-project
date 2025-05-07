<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLinkHistory extends Model
{
    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class);
    }
}
