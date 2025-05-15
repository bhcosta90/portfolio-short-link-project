<?php

declare(strict_types = 1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->configureUrl();
        $this->configureModel();
        $this->configureTests();
        $this->configurePassword();
        $this->configureDB();
        $this->configureVite();
        $this->configureDate();
    }

    public function boot(): void
    {
        //
    }

    private function configureUrl(): void
    {
        URL::forceHttps(app()->isProduction());
    }

    private function configureModel(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
    }

    private function configureTests(): void
    {
        Http::preventStrayRequests(!app()->isProduction());
    }

    private function configurePassword(): void
    {
        if (app()->isProduction()) {
            Password::defaults(fn () => Password::min(12)->max(100)->uncompromised());
        }
    }

    private function configureDB(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configureDate(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
