<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Excel\ShortLink\ImportByExcelAction;
use App\Http\Resources\ShortLinkClickResource;
use App\Http\Resources\ShortLinkResource;
use App\Services\ShortLinkClickService;
use App\Services\ShortLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

final class ShortLinkController extends Controller
{
    public function index(ShortLinkService $service): AnonymousResourceCollection
    {
        return ShortLinkResource::collection($service->index(auth()->id()));
    }

    public function store(Request $request, ShortLinkService $service): ShortLinkResource
    {
        return new ShortLinkResource($service->store(auth()->id(), $request->all()));
    }

    public function show(int $id, ShortLinkService $service): ShortLinkResource
    {
        return new ShortLinkResource($service->show($id));
    }

    public function clicks(int $id, ShortLinkService $service): AnonymousResourceCollection
    {
        return ShortLinkClickResource::collection($service->clicks($id));
    }

    public function imports(Request $request): void
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new ImportByExcelAction($request->user()->id), request()->file('file'));
    }

    public function redirectId(string $code, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('code_' . $code, now()->addHour(), static fn () => $service->queryRedirect()
            ->whereCode($code)
            ->firstOrFail()
            ->toArray());

        return $this->responseShortLink($shortLink);
    }

    public function redirectSlug(string $slug, ShortLinkService $service): RedirectResponse | string
    {
        $shortLink = Cache::remember('slug_' . $slug, now()->addHour(), static fn () => $service->queryRedirect()
            ->whereSlug($slug)
            ->firstOrFail()
            ->toArray());

        return $this->responseShortLink($shortLink);
    }

    private function responseShortLink(array $data): RedirectResponse | string
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
