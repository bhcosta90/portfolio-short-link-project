<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController
{
    public function sendCode(UserService $userService, Request $request): JsonResponse
    {
        $userService->sendCode($request->all());

        return response()->json([
            'message' => __('Code sent successfully'),
        ]);
    }

    public function login(UserService $userService, Request $request): JsonResponse
    {
        $user = $userService->login($request->all());

        if ($user instanceof \App\Models\User) {
            return response()->json([
                'token' => $user->createToken('api-service')->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => __('Logout successfully'),
        ]);
    }
}
