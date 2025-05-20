<?php

declare(strict_types = 1);

namespace App\Actions\ShortLinkClick;

use App\Events\ShortLinkClick\ShortLinkClickRecordedEvent;
use App\Http\Requests\ShortLinkClick\StoreRequest;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Core\Actions\AsAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

final class ClickAction
{
    use AsAction;

    protected function execute(array $data): ShortLinkClick
    {
        $shortLink = ShortLink::query()->findOrFail($data['id']);

        $click = $shortLink->shortLinkClicks()->create(Arr::except($data, 'id'));

        ShortLinkClickRecordedEvent::dispatch(
            $click->id,
            $data['ip_address'],
        );

        return $click;
    }

    protected function request(): FormRequest
    {
        return new StoreRequest();
    }
}
