<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\CreateClickShortLink;
use App\Events\CreatedClickShortLinkEvent;
use App\Models\ShortLink;
use Illuminate\Contracts\Queue\ShouldQueue;

final class AddClickOnShortLinkListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(CreateClickShortLink $event): void
    {
        $shortLink = ShortLink::query()->findOrFail($event->id);

        $click = $shortLink?->shortLinkClicks()->create([
            'ip_address' => $event->ipAddress,
            'endpoint'   => $event->endpoint,
        ]);

        event(new CreatedClickShortLinkEvent(
            id: $click->id,
            ipAddress: $event->ipAddress,
        ));
    }
}
