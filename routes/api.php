<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    Route::apiResource('short-links', ShortLinkController::class)
        ->only(['index', 'store', 'show'])
        ->middleware(request()->header('Authorization') ? 'auth:sanctum' : null);
});
