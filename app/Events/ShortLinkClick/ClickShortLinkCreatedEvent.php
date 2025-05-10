<?php

declare(strict_types = 1);

namespace App\Events\ShortLinkClick;

use Illuminate\Foundation\Events\Dispatchable;

final class ClickShortLinkCreatedEvent
{
    use Dispatchable;

    public function __construct(
        public int $id,
        public string $ipAddress,
    ) {
    }
}
