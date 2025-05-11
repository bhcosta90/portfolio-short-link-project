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

    public function login(UserService $userService, Request $request)
    {
        $user = $userService->login($request->all());

        if ($user) {
            return response()->json([
                'token' => $user->createToken('api-service')->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
