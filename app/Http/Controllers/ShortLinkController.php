<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\ShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLink;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
        $shortLink = ShortLink::create($request->validated() + [
            'user_id' => Auth::id(),
        ]);

        return new ShortLinkResource($shortLink->refresh());
    }

    public function show(ShortLink $shortLink): ShortLinkResource
    {
        return new ShortLinkResource($shortLink);
    }
}
