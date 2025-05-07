<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\ShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

final class ShortLinkController extends Controller
{
    public function index(): AnonymousResourceCollection
    {

        abort_unless(Auth::check(), 403, __('Unauthorized access'));

        $result = ShortLink::query()
            ->byUser(Auth::id())
            ->simplePaginate();

        return ShortLinkResource::collection($result);
    }

    public function store(ShortLinkRequest $request): ShortLinkResource
    {
        $shortLink = ShortLink::create([
            'slug'       => str()->slug($request->slug),
            'user_id'    => Auth::id(),
            'is_premium' => when(Auth::user()?->is_premium, true),
        ] + $request->validated());

        return new ShortLinkResource($shortLink->refresh());
    }

    public function show(string $short_link): ShortLinkResource
    {
        $shortLink = ShortLink::query()
            ->whereId(Hashids::decode($short_link))
            ->firstOrFail();

        return new ShortLinkResource($shortLink);
    }

    public function redirectId(string $hashId): RedirectResponse | string
    {
        $shortLink = ShortLink::query()
            ->whereId(Hashids::decode($hashId))
            ->firstOrFail();

        return $this->responseShortLink($shortLink);
    }

    public function redirectSlug(string $slug): RedirectResponse | string
    {
        $shortLink = ShortLink::query()
            ->whereSlug($slug)
            ->firstOrFail();

        return $this->responseShortLink($shortLink);
    }

    private function responseShortLink(ShortLink $shortLink): RedirectResponse | string
    {
        if (app()->isLocal()) {
            return __('Vai ser redirecionado para o endpoint: :endpoint', [
                'endpoint' => $shortLink->endpoint,
            ]);
        }

        return response()->redirectTo($shortLink->endpoint);
    }
}
