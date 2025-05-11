<?php

declare(strict_types = 1);

namespace Core\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidateService
{
    protected function validate(array $data, ?array $rules = null): array
    {
        if (null === $rules) {
            $backtrace    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller       = ucfirst($backtrace[1]['function'] ?? '');
            $className    = self::class;
            $serviceName  = class_basename($className);
            $trimmedName  = preg_replace('/Service$/', '', $serviceName);
            $namespace    = mb_substr($className, 0, mb_strrpos($className, '\\'));
            $classRequest = "{$namespace}\\Requests\\$trimmedName\\{$caller}Request";

            $request = new $classRequest();
            $request->replace($data);

            abort_unless($request->authorize(), 403, __('Unauthorized action.'));

            return $request->validate($request->rules());
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
