<?php

declare(strict_types = 1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('send-code', 'sendCode');
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware('auth:sanctum');
});
