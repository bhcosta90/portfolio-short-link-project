<?php

declare(strict_types = 1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

final class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal): bool {
            if ($isLocal) {
                return true;
            }

            if ($entry->isReportableException()) {
                return true;
            }

            if ($entry->isFailedRequest()) {
                return true;
            }

            if ($entry->isFailedJob()) {
                return true;
            }

            if ($entry->isScheduledTask()) {
                return true;
            }

            return $entry->hasMonitoredTag();
        });
    }

    protected function gate(): void
    {
        Gate::define('viewTelescope', fn ($user): bool => in_array($user->email, [
            //
        ]));
    }

    private function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
