<?php

declare(strict_types = 1);

namespace Core\Validation;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidateAction
{
    protected function validate(array $data, ?array $rules = null): array
    {
        if (null === $rules) {
            $className    = self::class;
            $namespace    = mb_substr($className, 0, mb_strrpos($className, '\\'));
            $parts        = explode('\\', $namespace);
            $lastPart     = array_pop($parts);
            $serviceName  = class_basename($className);
            $trimmedName  = preg_replace('/Action$/', '', $serviceName);
            $classRequest = "App\\Http\\Requests\\{$lastPart}\\Actions\\{$trimmedName}Request";

            if (config('laravel-package.request')) {
                $namespace    = mb_substr($className, 0, mb_strrpos($className, '\\'));
                $classRequest = "{$namespace}\\Requests\\{$trimmedName}Request";
            }

            $request = new $classRequest();
            $request->replace($data);

            abort_unless($request->authorize(), Response::HTTP_FORBIDDEN, __('Unauthorized action.'));

            return $request->validate($request->rules());
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
