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
use Illuminate\Support\Facades\Cache;
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
        $shortLink = Cache::remember('id_' . $hashId, 60 * 24, static function () use ($hashId) {
            return ShortLink::query()
                ->whereId(Hashids::decode($hashId))
                ->firstOrFail()
                ->endpoint;
        });

        return $this->responseShortLink($shortLink);
    }

    public function redirectSlug(string $slug): RedirectResponse | string
    {
        $shortLink = Cache::remember('slug_' . $slug, 60 * 24, static function () use ($slug) {
            return ShortLink::query()
                ->whereSlug($slug)
                ->firstOrFail()
                ->endpoint;
        });

        return $this->responseShortLink($shortLink);
    }

    protected function responseShortLink(string $endpoint): RedirectResponse | string
    {
        if (app()->isLocal()) {
            return __('Vai ser redirecionado para o endpoint: :endpoint', [
                'endpoint' => $endpoint,
            ]);
        }

        return response()->redirectTo($endpoint);
    }
}
