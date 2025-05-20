<?php

declare(strict_types = 1);

use Illuminate\Database\Eloquent\Factories\HasFactory;

arch('php')->preset()->php();

arch('app')
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump'])
    ->toUseStrictTypes();

arch('actions')
    ->expect('App\Actions')
    ->not->toHavePublicMethods()
    ->toHaveSuffix('Action');

arch('models')
    ->expect('App\Models')
    ->toUseTraits([HasFactory::class]);
