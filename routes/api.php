<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('short-links', ShortLinkController::class)
        ->only(['index', 'store'])
        ->middleware(request()->header('Authorization') ? 'auth:sanctum' : null);
});
