<?php

declare(strict_types = 1);

namespace App\Services;

use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;

final readonly class ShortLinkClickService
{
    public function store(array $data): ShortLinkClick
    {
        $shortLink = ShortLink::query()->findOrFail($data['id']);

        $click = $shortLink?->shortLinkClicks()->create([
            'ip_address' => $data['ip_address'],
            'endpoint'   => $data['endpoint'],
        ]);

        event(new ShortLinkClickRecordedEvent(
            id: $click->id,
            ipAddress: $data['ip_address'],
        ));

        return $click;
    }
}
