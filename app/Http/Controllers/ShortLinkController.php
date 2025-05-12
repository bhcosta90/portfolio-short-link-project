<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\ShortLinkClickResource;
use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLinkClick;
use App\Services\ShortLinkClickService;
use App\Services\ShortLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ShortLinkController extends Controller
{
    public function index(ShortLinkService $service): AnonymousResourceCollection
    {
        $result = $service->index(auth()->id())
            ->simplePaginate();

        return ShortLinkResource::collection($result);
    }

    public function store(Request $request, ShortLinkService $service): ShortLinkResource
    {
        $shortLink = $service->store(auth()->id(), $request->all());

        return new ShortLinkResource($shortLink->refresh());
    }

    public function show(int $id, ShortLinkService $service): ShortLinkResource
    {
        return new ShortLinkResource($service->show($id));
    }

    public function clicks(int $id): AnonymousResourceCollection
    {
        $clicks = ShortLinkClick::query()
            ->with([
                'geoIp' => fn ($query) => $query->whereIsSuccess(true),
            ])
            ->whereShortLinkId($id)
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
                return __('It will be redirected to endpoint: :endpoint', [
                    'endpoint' => $data['endpoint'],
                ]);
            }

            return response()->redirectTo($data['endpoint']);
        });
    }
}
