<?php

declare(strict_types = 1);

namespace Core\Actions;

use Illuminate\Support\Facades\Concurrency;
use RuntimeException;

trait AsAction
{
    use ValidateAction;

    final public static function run(...$arguments)
    {
        $result = app(static::class);

        if (!method_exists($result, 'execute')) {
            throw new RuntimeException('The execute method is not defined in the action class.');
        }

        return app(static::class)
            ->execute($result->validate($arguments[0]));
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
