<?php

namespace Bogochow\Pulse\Packages\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Attributes\Lazy;

class NpmPackages extends PackagesCard
{
    protected string $installedKey = 'current';

    #[Lazy]
    public function render()
    {
        $packages = Pulse::values('npm_packages', ['outdated', 'time']);

        $outdatedPackages = isset($packages['outdated'])
        ? json_decode($packages['outdated']->value, associative: true, flags: JSON_THROW_ON_ERROR)
        : [];

        $outdatedPackages = $this->parsePackages($outdatedPackages);

        return View::make('packages::livewire.npm_packages', [
            'outdated' => $outdatedPackages,
            'time' => $packages?->get('time')?->value ?? null,
        ]);
    }
}
