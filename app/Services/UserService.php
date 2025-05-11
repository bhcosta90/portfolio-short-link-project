<?php

declare(strict_types = 1);

namespace App\Services;

use Core\Validation\ValidateService;

final class UserService
{
    use ValidateService;

    public function sendCode(array $data)
    {
        $this->validate($data);
    }
}
