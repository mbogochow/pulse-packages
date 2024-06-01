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
        // Get the data out of the Pulse data store.
        $outdated = Pulse::values('composer_packages', ['time', 'result']);

        $packages = isset($outdated['result'])
            ? json_decode($outdated['result']->value, associative: true, flags: JSON_THROW_ON_ERROR)['installed']
            : [];

        $packages = $this->parsePackages($packages);

        return View::make('packages::livewire.composer_packages', [
            'packages' => $packages,
            'time' => $outdated?->get('time')?->value ?? null,
        ]);
    }
}
