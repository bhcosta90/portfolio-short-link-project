<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use Core\Actions\AsAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

final class LoginAction
{
    use AsAction;

    protected function execute($data): ?User
    {
        $user = User::query()
            ->whereEmail($data['email'])
            ->first();

        if ($user && Hash::check($data['code'], $user->token)) {
            $user->token = null;

            if ($user->email_verified_at) {
                $user->email_verified_at = now();
            }

            $user->save();

            return $user;
        }

        return null;
    }

    protected function request(): FormRequest
    {
        return new LoginRequest();
    }
}
