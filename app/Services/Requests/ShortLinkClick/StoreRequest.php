<?php

declare(strict_types = 1);

namespace App\Services\Requests\ShortLinkClick;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'endpoint'   => ['required'],
            'ip_address' => ['required', 'ip'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
