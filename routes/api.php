<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function (): void {
    Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

    Route::prefix('v1')->group(function (): void {

        Route::prefix('short-links/{shortLink}')->group(function () {
            Route::get('clicks', [ShortLinkController::class, 'clicks']);
        });

        Route::apiResource('short-links', ShortLinkController::class)
            ->only(['index', 'store', 'show'])
            ->middleware(request()->header('Authorization') ? 'auth:sanctum' : null);
    });
});

Route::get('/r/{hashId}', [ShortLinkController::class, 'redirectId'])->name('link-short.redirect.id');
Route::get('/s/{shortLink:code}', [ShortLinkController::class, 'redirectSlug'])->name('link-short.redirect.slug');
