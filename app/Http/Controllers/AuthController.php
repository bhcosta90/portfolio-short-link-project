<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

final class AuthController
{
    public function sendCode(UserService $userService, Request $request)
    {
        $userService->sendCode($request->all());
    }
}
