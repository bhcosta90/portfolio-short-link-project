<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

final class SendCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
