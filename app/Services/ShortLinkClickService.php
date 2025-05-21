<?php

declare(strict_types = 1);

namespace App\Services;

use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Http\Requests\ShortLinkClick\StoreRequest;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Core\Services\ValidateService;
use Illuminate\Support\Arr;

final readonly class ShortLinkClickService
{
    use ValidateService;

    public function store(array $data): ShortLinkClick
    {
        $dataValidated = $this->validate($data, new StoreRequest());

        $shortLink = ShortLink::query()->findOrFail($data['id']);

        $click = $shortLink->shortLinkClicks()->create(Arr::except($dataValidated, 'id'));

        ShortLinkClickRecordedEvent::dispatch(
            $click->id,
            $data['ip_address'],
        );

        return $click;
    }
}
