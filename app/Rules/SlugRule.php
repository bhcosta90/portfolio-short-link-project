<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\ShortLink;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class SlugRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (filled($value)
            && auth()->check()
            && !auth()->user()->can('registerSlugs', ShortLink::class)) {
            $fail(__('You are not allowed to record slugs, please become premium.'));
        }

    }
}
