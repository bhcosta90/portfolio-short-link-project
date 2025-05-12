<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\AsHashed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ShortLink extends Model
{
    use AsHashed;
    use HasFactory;

    public const TOTAL_DAYS_FREE    = 3;
    public const TOTAL_DAYS_PREMIUM = 7;

    protected $fillable = [
        'user_id',
        'slug',
        'endpoint',
        'is_premium',
        'expired_at',
        'code',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function scopeByUser(Builder $query, ?int $userId): void
    {
        $query->when(
            $userId,
            fn (Builder $query) => $query->where('user_id', $userId)
        );
    }

    public function scopeOnlyValidated(Builder $query, bool $accept = false): void
    {
        $query->when(
            $accept,
            fn (Builder $query) => $query->where('expired_at', '>=', now())
        );
    }

    public function shortLink(): Attribute
    {
        return Attribute::get(
            fn () => route(
                when($this->slug, 'link-short.redirect.slug', 'link-short.redirect.id'),
                $this->slug ?: $this->code
            )
        );
    }

    public function quantityDaysExpiredAt(): Attribute
    {
        return Attribute::get(
            fn () => (int) ceil($this->expired_at->diffInDays() * -1)
        );
    }

    public function shortLinkClicks(): HasMany
    {
        return $this->hasMany(ShortLinkClick::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
