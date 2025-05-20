<?php

declare(strict_types = 1);

namespace App\Services;

use App\Actions\ShortLink\CreateShortLinkAction;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

final readonly class ShortLinkService
{
    use AuthorizesRequests;

    public function index(?int $idUser): Paginator
    {
        abort_unless(
            $idUser,
            Response::HTTP_FORBIDDEN,
            __('Unauthorized access')
        );

        return ShortLink::query()
            ->withCount([
                'shortLinkClicks',
            ])
            ->byUser($idUser)
            ->simplePaginate();
    }

    public function show(int $id): ShortLink
    {
        $shortLink = ShortLink::query()
            ->withCount([
                'shortLinkClicks',
            ])
            ->whereId($id)
            ->sole();

        if ($shortLink->user_id) {
            $this->authorize('view', $shortLink);
        }

        return $shortLink;
    }

    public function store(?int $idUser, array $data): ShortLink
    {
        return CreateShortLinkAction::run($data + ['user_id' => $idUser]);
    }

    public function clicks(int $id): Paginator
    {
        return ShortLinkClick::query()
            ->with([
                'geoIp' => fn ($query) => $query->whereIsSuccess(true),
            ])
            ->whereShortLinkId($id)
            ->orderBy('id', 'desc')
            ->simplePaginate();
    }

    /** @return Builder<ShortLink> */
    public function queryRedirect(): Builder
    {
        return ShortLink::query()
            ->select(['id', 'endpoint'])
            ->onlyValidated(true);
    }
}
