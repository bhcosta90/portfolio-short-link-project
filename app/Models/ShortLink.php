<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ShortLink extends Model
{
    use AsHashed;
    use HasFactory;
    protected $fillable = [
        'user_id',
        'slug',
        'endpoint',
        'is_premium',
    ];

    #[Scope]
    public function byUser(Builder $query, ?int $userId): void
    {
        $query->when(
            $userId,
            fn (Builder $query) => $query->where('user_id', $userId)
        );
    }
}
