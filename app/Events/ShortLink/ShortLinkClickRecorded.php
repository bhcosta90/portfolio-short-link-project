<?php

declare(strict_types = 1);

namespace App\Events\ShortLink;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class ShortLinkClickRecorded
{
    use Dispatchable;

    public function __construct(
        public int $id,
        public string $endpoint,
        public string $ipAddress,
    ) {
    }
}
