<?php

declare(strict_types = 1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('send-code', 'sendCode');
});
