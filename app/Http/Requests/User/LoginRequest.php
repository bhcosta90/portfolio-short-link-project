<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
