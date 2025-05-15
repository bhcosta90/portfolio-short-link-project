<?php

declare(strict_types = 1);

namespace App\Models\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait AsHashed
{
    public static function bootAsHashed(): void
    {
        static::retrieved(fn ($model) => $model->addHashedIds());

        static::saving(fn ($model) => $model->removeHashedIds());
    }

    protected function addHashedIds(): void
    {
        collect($this->getAttributes())
            ->filter(fn ($value, $key) => $this->isHashableKey($key))
            ->each(fn ($value, $key) => $this->{"hash_{$key}"} = when($value, Hashids::encode($value)));
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
