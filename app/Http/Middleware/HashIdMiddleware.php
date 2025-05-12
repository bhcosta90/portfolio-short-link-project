<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Core\HashId\Libs\Cryptography\Decoder;
use Core\HashId\Libs\Cryptography\Encoder;
use Illuminate\Http\Request;

final class HashIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $enabledForCryptography = $this->isEnabledForCryptography($request);

        if ($enabledForCryptography) {
            $request = Decoder::run($request);
            $request->merge(['beenEncrypted' => false]);
        }

        $response = $next($request);

        if ($enabledForCryptography) {
            $response = Encoder::run($response);
            $request->merge(['beenEncrypted' => true]);
        }

        return $response;
    }

    private function isEnabledForCryptography($request): bool
    {
        return config('hashids.enable_cryptography', false) && ($request->beenEncrypted ?? true);
    }
}
