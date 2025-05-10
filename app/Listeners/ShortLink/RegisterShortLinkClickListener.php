<?php

declare(strict_types = 1);

namespace App\Listeners\ShortLink;

use App\Events\ShortLink\ShortLinkClickRecorded;
use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Models\ShortLink;
use Illuminate\Contracts\Queue\ShouldQueue;

final class RegisterShortLinkClickListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(ShortLinkClickRecorded $event): void
    {
        $shortLink = ShortLink::query()->findOrFail($event->id);

        $click = $shortLink?->shortLinkClicks()->create([
            'ip_address' => $event->ipAddress,
            'endpoint'   => $event->endpoint,
        ]);

        event(new ShortLinkClickRecordedEvent(
            id: $click->id,
            ipAddress: $event->ipAddress,
        ));
    }
}
