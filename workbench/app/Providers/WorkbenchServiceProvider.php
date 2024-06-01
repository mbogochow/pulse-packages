<?php

namespace Workbench\App\Providers;

use Bogochow\Pulse\Packages\Recorders\PackagesRecorder;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        config(['pulse.recorders' => [
            PackagesRecorder::class => []
        ]]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
