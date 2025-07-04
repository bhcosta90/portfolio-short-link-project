<?php

declare(strict_types = 1);

use App\Http\Controllers\ShortLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('{shortLink}')->group(function (): void {
    Route::get('clicks', [ShortLinkController::class, 'clicks']);
});

Route::post('imports', [ShortLinkController::class, 'imports']);
