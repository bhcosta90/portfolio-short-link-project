<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('{shortLink}')->group(function () {
    Route::get('clicks', [ShortLinkController::class, 'clicks']);
});

Route::apiResource('', ShortLinkController::class)
    ->only(['index', 'store', 'show'])
    ->middleware(request()->header('Authorization') ? 'auth:sanctum' : null);
