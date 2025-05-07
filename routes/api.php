<?php

use App\Http\Controllers\ShortLinkController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('short-links', ShortLinkController::class);
});

//Route::get('test', function(){
//    User::first()
//})
