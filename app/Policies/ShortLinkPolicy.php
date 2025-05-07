<?php

declare(strict_types = 1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

final class ShortLinkPolicy
{
    use HandlesAuthorization;

    public function registerSlugs(): bool
    {
        return true;
    }
}
