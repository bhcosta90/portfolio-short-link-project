<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use App\Http\Resources\UserResource;
use Core\HashId\Middleware\HashIdMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware([HashIdMiddleware::class])->group(function (): void {
    Route::prefix('v1')->group(function (): void {
        Route::prefix('short-links')->middleware('auth:sanctum')->group(function (): void {
            include __DIR__ . '/v1/short_links.php';
        });
        Route::apiResource('short-links', ShortLinkController::class)
            ->only(['index', 'store', 'show'])
            ->middleware(request()->header('Authorization') ? 'auth:sanctum' : null);

        Route::prefix('auth')->group(function (): void {
            include __DIR__ . '/v1/auth.php';
        });

        Route::prefix('user')->group(function (): void {
            Route::get('/me', fn (Request $request): UserResource => new UserResource($request->user()))->middleware('auth:sanctum');
        });
    });
});

Route::get('/r/{hashId}', [ShortLinkController::class, 'redirectId'])->name('link-short.redirect.id');
Route::get('/s/{shortLink:code}', [ShortLinkController::class, 'redirectSlug'])->name('link-short.redirect.slug');
