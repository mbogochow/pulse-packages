<?php

namespace Bogochow\Pulse\Packages;

use Bogochow\Pulse\Packages\Commands\PulsePackagesClearCommand;
use Bogochow\Pulse\Packages\Livewire\ComposerPackages;
use Bogochow\Pulse\Packages\Livewire\NpmPackages;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireManager;

class PulsePackagesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'packages');

        $this->callAfterResolving('livewire', function (LivewireManager $livewire, Application $app) {
            $livewire->component('composer_packages', ComposerPackages::class);
            $livewire->component('npm_packages', NpmPackages::class);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                PulsePackagesClearCommand::class,
            ]);
        }
    }
}
