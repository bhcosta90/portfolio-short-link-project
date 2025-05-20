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
        $instance = self::getInstance();

        if (!method_exists($instance, 'execute')) {
            throw new RuntimeException('The execute method is not defined in the action class.');
        }

        return $instance->execute($instance->validate($arguments[0]));
    }

    final public static function dispatch(...$arguments): void
    {
        dispatch(fn () => self::run(...$arguments));
    }

    final public static function defer(...$arguments): void
    {
        Concurrency::defer(fn () => self::run(...$arguments));
    }

    private static function getInstance(): self
    {
        return app(static::class);
    }
}
