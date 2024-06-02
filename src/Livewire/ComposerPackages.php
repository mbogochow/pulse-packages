<?php

namespace Bogochow\Pulse\Packages\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Attributes\Lazy;

class ComposerPackages extends PackagesCard
{
    public bool $showAge = false;

    #[Lazy]
    public function render()
    {
        $composerData = Pulse::values('composer_packages', ['time', 'outdated', 'audit']);

        $outdatedPackages = isset($composerData['outdated'])
            ? json_decode($composerData['outdated']->value, associative: true, flags: JSON_THROW_ON_ERROR)['installed']
            : [];
        $auditedPackages = isset($composerData['audit'])
            ? json_decode($composerData['audit']->value, associative: true, flags: JSON_THROW_ON_ERROR)
            : [];

        $outdatedPackages = $this->parsePackages($outdatedPackages);

        return View::make('packages::livewire.composer_packages', [
            'outdated' => $outdatedPackages,
            'audit' => $auditedPackages,
            'time' => $composerData?->get('time')?->value ?? null,
        ]);
    }
}
