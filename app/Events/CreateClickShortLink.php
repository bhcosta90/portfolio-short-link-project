<?php

declare(strict_types = 1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class CreateClickShortLink
{
    use Dispatchable;

    public function __construct(
        public int $id,
        public string $endpoint,
        public string $ipAddress,
    ) {
    }
}
