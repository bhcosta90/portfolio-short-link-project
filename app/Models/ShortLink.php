<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model {
    protected $fillable = [
        'user_id',
        'slug',
        'endpoint',
    ];

    #[Scope]
    public function byUser(Builder $query, ?int $userId): void{
        $query->when(
            $userId,
            fn (Builder $query) => $query->where('user_id', $userId)
        );
    }
}
