<?php

declare(strict_types = 1);

namespace Core\Actions;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

trait ValidateAction
{
    protected function validate(array | Arrayable $data): array
    {
        $data = $data instanceof Arrayable ? $data->toArray() : $data;

        if (is_null($rules = $this->rules()) && method_exists($this, 'request')) {
            return $this->validateWithRequest($data);
        }

        return $this->validateWithRules($data, $rules);
    }

    protected function rules(): ?array
    {
        return null;
    }

    private function validateWithRequest(array $data): array
    {
        $request = $this->createRequestInstance();

        $request->replace($data);

        abort_unless($request->authorize(), Response::HTTP_FORBIDDEN, __('Unauthorized action.'));

        return $request->validate($request->rules());
    }

    private function createRequestInstance(): FormRequest
    {
        $classRequest = $this->request();

        $request = new $classRequest();

        if (!$request instanceof FormRequest) {
            throw new RuntimeException('The request must be an instance of FormRequest');
        }

        return $request;
    }

    private function validateWithRules(array $data, ?array $rules): array
    {
        $validator = Validator::make($data, $rules ?: []);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
