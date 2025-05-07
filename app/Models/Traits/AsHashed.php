<?php

declare(strict_types = 1);

namespace App\Models\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait AsHashed
{
    public static function bootAsHashed(): void
    {
        static::retrieved(static function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if ('id' === $key || str_ends_with($key, '_id')) {
                    $model->{"{$key}_hashed"} = Hashids::encode($value);
                }
            }
        });
    }
}
