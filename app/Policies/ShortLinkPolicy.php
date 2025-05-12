<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class ShortLinkPolicy
{
    use HandlesAuthorization;

    public function registerSlugs(User $user): bool
    {
        return $user->is_premium;
    }

    public function view(User $user, ShortLink $shortLink): bool
    {
        return $user->is($shortLink->user);
    }
}
