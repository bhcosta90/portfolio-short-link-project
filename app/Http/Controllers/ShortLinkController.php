<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShortLinkResource;
use App\Models\ShortLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ShortLinkController extends Controller
{
    public function __construct()
    {
        if(request()->header('Authorization')){
            $this->middleware('auth:sanctum')
                ->only('index');
        }
    }


    public function index(): AnonymousResourceCollection {

        abort_unless(Auth::check(), 403, __('Unauthorized access'));

        $result = ShortLink::query()
            ->byUser(Auth::id())
            ->simplePaginate();

        return ShortLinkResource::collection($result);
    }
}
