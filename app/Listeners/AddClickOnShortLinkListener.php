<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\RegisterClickShortLinkEvent;
use App\Models\ShortLink;
use Illuminate\Contracts\Queue\ShouldQueue;

final class AddClickOnShortLinkListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(RegisterClickShortLinkEvent $event): void
    {
        $shortLink = ShortLink::query()->findOrFail($event->id);

        $shortLink?->shortLinkClicks()->create([
            'ip_address' => $event->ipAddress,
            'endpoint'   => $event->endpoint,
        ]);
    }
}
