<?php

declare(strict_types = 1);

namespace App\Services;

use App\Actions\ShortLink\CreateShortLinkAction;
use App\Models\ShortLink;
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
        return app(CreateShortLinkAction::class)->handle($data + ['user_id' => $idUser]);
    }

    /** @return Builder<ShortLink> */
    public function queryRedirect(): Builder
    {
        return ShortLink::query()
            ->select(['id', 'endpoint'])
            ->onlyValidated(true);
    }
}
