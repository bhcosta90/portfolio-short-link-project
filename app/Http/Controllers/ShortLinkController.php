<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\ShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
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

    public function store(ShortLinkRequest $request, ShortLinkService $service): ShortLinkResource
    {
        $shortLink = $service->store(Auth::user(), $request->validated());

        return new ShortLinkResource($shortLink->refresh());
    }

    public function show(string $short_link): ShortLinkResource
    {
        $shortLink = ShortLink::query()
            ->whereId(Hashids::decode($short_link))
            ->firstOrFail();

        return new ShortLinkResource($shortLink);
    }

    public function redirectId(string $code, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('id_' . $code, 60 * 24, static function () use ($code, $service) {
            return $service->queryRedirect()
                ->whereCode($code)
                ->firstOrFail()
                ->endpoint;
        });

        return $this->responseShortLink($shortLink);
    }

    public function redirectSlug(string $slug, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('slug_' . $slug, 60 * 24, static function () use ($slug, $service) {
            return $service->queryRedirect()
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
