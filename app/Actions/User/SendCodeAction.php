<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Http\Requests\User\SendCodeRequest;
use App\Models\User;
use App\Notifications\User\SendCodeNotification;
use Core\Actions\AsAction;
use Illuminate\Foundation\Http\FormRequest;

final class SendCodeAction
{
    use AsAction;

    protected function execute($data): bool
    {
        $user = User::query()
            ->whereEmail($data['email'])
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

    protected function request(): FormRequest
    {
        return new SendCodeRequest();
    }
}
