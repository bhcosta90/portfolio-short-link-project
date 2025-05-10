<?php

declare(strict_types = 1);

namespace App\Services;

use App\Actions\ShortLink\CreateShortLinkAction;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Builder;

final readonly class ShortLinkService
{
    public function store(array $data): ShortLink
    {
        return app(CreateShortLinkAction::class)->handle($data);
    }

    public function queryRedirect(): Builder
    {
        return ShortLink::query()
            ->select(['id', 'endpoint'])
            ->onlyValidated(true);
    }
}
