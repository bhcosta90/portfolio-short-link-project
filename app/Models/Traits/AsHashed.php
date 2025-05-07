<?php

declare(strict_types = 1);

namespace App\Models\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait AsHashed
{
    public static function bootAsHashed(): void
    {
        static::retrieved(function ($model): void {
            $model->addHashedIds();
        });

        static::saving(function ($model): void {
            $model->removeHashedIds();
        });
    }

    protected function addHashedIds(): void
    {
        foreach ($this->getAttributes() as $key => $value) {
            if ($this->isHashableKey($key)) {
                $this->{"hash_{$key}"} = Hashids::encode($value);
            }
        }
    }

    protected function removeHashedIds(): void
    {
        foreach ($this->getAttributes() as $key => $value) {
            if ($this->isHashableKey($key)) {
                unset($this->{"hash_{$key}"});
            }
        }
    }

    protected function isHashableKey(string $key): bool
    {
        return 'id' === $key || str_ends_with($key, '_id');
    }
}
