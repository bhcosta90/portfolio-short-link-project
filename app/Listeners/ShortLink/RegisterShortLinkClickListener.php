<?php

declare(strict_types = 1);

namespace App\Listeners\ShortLink;

use App\Events\ShortLink\ClickShortLinkCreate;
use App\Events\ShortLinkClick\ClickShortLinkCreatedEvent;
use App\Models\ShortLink;
use Illuminate\Contracts\Queue\ShouldQueue;

final class RegisterShortLinkClickListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(ClickShortLinkCreate $event): void
    {
        $shortLink = ShortLink::query()->findOrFail($event->id);

        $click = $shortLink?->shortLinkClicks()->create([
            'ip_address' => $event->ipAddress,
            'endpoint'   => $event->endpoint,
        ]);

        event(new ClickShortLinkCreatedEvent(
            id: $click->id,
            ipAddress: $event->ipAddress,
        ));
    }
}
