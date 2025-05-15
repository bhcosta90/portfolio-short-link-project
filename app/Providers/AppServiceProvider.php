<?php

declare(strict_types = 1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

    protected function configureUrl(): void
    {
        URL::forceHttps(app()->isProduction());
    }

    protected function configureModel(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
    }

    protected function configureTests(): void
    {
        Http::preventStrayRequests(!app()->isProduction());
    }

    protected function configurePassword(): void
    {
        if (app()->isProduction()) {
            Password::defaults(fn () => Password::min(12)->max(100)->uncompromised());
        }
    }

    protected function configureDB(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    protected function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    protected function configureDate(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
