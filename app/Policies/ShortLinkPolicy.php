<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class ShortLinkPolicy
{
    use HandlesAuthorization;

    public function registerSlugs(User $user): bool
    {
        return $user->is_premium;
    }
}
