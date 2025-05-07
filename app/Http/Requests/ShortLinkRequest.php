<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ShortLinkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required'],
            'slug'     => ['nullable', 'string', 'max:255', Rule::unique('short_links', 'slug')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
