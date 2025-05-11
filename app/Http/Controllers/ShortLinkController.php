<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\ShortLinkRequest;
use App\Http\Resources\ShortLinkClickResource;
use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use App\Services\ShortLinkClickService;
use App\Services\ShortLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $shortLink = $service->store($request->validated() + [
            'user_id' => auth()?->id(),
        ]);

        return new ShortLinkResource($shortLink->refresh());
    }

    public function show(string $short_link): ShortLinkResource
    {
        $shortLink = ShortLink::query()
            ->with([
                'shortLinkClicks',
            ])
            ->whereId(Hashids::decode($short_link))
            ->firstOrFail();

        return new ShortLinkResource($shortLink);
    }

    public function clicks(string $short_link): AnonymousResourceCollection
    {
        $clicks = ShortLinkClick::query()
            ->with([
                'geoIp' => fn ($query) => $query->whereIsSuccess(true),
            ])
            ->whereShortLinkId(Hashids::decode($short_link))
            ->orderBy('id', 'desc')
            ->paginate();

        return ShortLinkClickResource::collection($clicks);
    }

    public function redirectId(string $code, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('id_' . $code, 60 * 24, static function () use ($code, $service) {
            return $service->queryRedirect()
                ->whereCode($code)
                ->firstOrFail()
                ->toArray();
        });

        return $this->responseShortLink($shortLink);
    }

    public function redirectSlug(string $slug, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('slug_' . $slug, 60 * 24, static function () use ($slug, $service) {
            return $service->queryRedirect()
                ->whereSlug($slug)
                ->firstOrFail()
                ->toArray();
        });

        return $this->responseShortLink($shortLink);
    }

    protected function responseShortLink(array $data): RedirectResponse | string
    {
        $ip = request()->ip();

        if ($newIp = config('geo-ip.ip')) {
            $ip = $newIp;
        }

        return DB::transaction(function () use ($data, $ip) {
            app(ShortLinkClickService::class)->store([
                'id'         => $data['id'],
                'ip_address' => $ip,
                'endpoint'   => $data['endpoint'],
            ]);

            if (app()->isLocal()) {
                return __('Vai ser redirecionado para o endpoint: :endpoint', [
                    'endpoint' => $data['endpoint'],
                ]);
            }

            return response()->redirectTo($data['endpoint']);
        });
    }
}
