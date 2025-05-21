<?php

declare(strict_types = 1);

namespace Core\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidateService
{
    protected function validate(array $data, array | FormRequest | null $validated = null): array
    {
        if ($validated instanceof FormRequest) {
            $request = new $validated();
            $request->replace($data);

            abort_unless($request->authorize(), Response::HTTP_FORBIDDEN, __('Unauthorized action.'));

            return $request->validate($request->rules());
        }

        if (is_array($validated)) {
            $validator = Validator::make($data, $validated);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return $validator->validated();
        }

        return [];
    }
}
