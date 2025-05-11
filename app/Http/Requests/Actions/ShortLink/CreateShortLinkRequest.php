<?php

declare(strict_types = 1);

namespace App\Http\Requests\Actions\ShortLink;

use App\Rules\SlugRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateShortLinkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required'],
            'slug'     => [
                'nullable',
                'string',
                'max:55',
                Rule::unique('short_links', 'slug'),
                new SlugRule(),
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
