<?php

declare(strict_types = 1);

namespace App\Services;

use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Core\Validation\ValidateService;

final readonly class ShortLinkClickService
{
    use ValidateService;

    public function store(array $data): ShortLinkClick
    {
        $dataValidated = $this->validate($data);

        $shortLink = ShortLink::query()->findOrFail($data['id']);

        $click = $shortLink?->shortLinkClicks()->create($dataValidated);

        event(new ShortLinkClickRecordedEvent(
            id: $click->id,
            ipAddress: $data['ip_address'],
        ));

        return $click;
    }
}
