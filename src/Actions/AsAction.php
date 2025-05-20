<?php

declare(strict_types = 1);

namespace Core\Actions;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Concurrency;

trait AsAction
{
    use ValidateAction;

    abstract protected function execute(array | Arrayable $data);

    final public static function run(...$arguments)
    {
        $result = app(static::class);
        $data   = $result->validate($arguments[0]);

        return app(static::class)->execute($data);
    }

    final public static function dispatch(...$arguments): void
    {
        dispatch(fn () => app(static::class)::run(...$arguments));
    }

    final public static function defer(...$arguments): void
    {
        Concurrency::defer(fn () => app(static::class)::run(...$arguments));
    }
}
