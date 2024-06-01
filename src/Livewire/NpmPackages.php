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
        $outdated = Pulse::values('npm_packages', ['result', 'time']);

        $packages = isset($outdated['result'])
        ? json_decode($outdated['result']->value, associative: true, flags: JSON_THROW_ON_ERROR)
        : [];

        $packages = $this->parsePackages($packages);

        return View::make('packages::livewire.npm_packages', [
            'packages' => $packages,
            'time' => $outdated?->get('time')?->value ?? null,
        ]);
    }
}
