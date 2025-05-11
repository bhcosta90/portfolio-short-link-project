<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\User;
use App\Notifications\User\SendCodeNotification;
use Core\Validation\ValidateService;
use Illuminate\Support\Facades\Hash;

final class UserService
{
    use ValidateService;

    public function sendCode(array $data): bool
    {
        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if ($user) {
            $code        = random_int(100000, 999999);
            $user->token = $code;
            $user->save();

            $user->notify(new SendCodeNotification((string) $code));

            return true;
        }

        return false;
    }

    public function login(array $data): ?User
    {
        $dataValidated = $this->validate($data);

        $user = User::query()
            ->whereEmail($data['email'])
            ->first();

        if ($user && Hash::check($dataValidated['code'], $user->token)) {
            $user->token = null;
            $user->save();

            return $user;
        }

        return null;
    }
}
