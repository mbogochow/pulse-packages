<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace AaronFrancis\Pulse\Outdated\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

class NpmOutdated extends Card
{
    #[Lazy]
    public function render()
    {
        $outdated = Pulse::values('npm_outdated', ['result', 'time']);

        $packages = isset($outdated['result'])
        ? json_decode($outdated['result']->value, associative: true, flags: JSON_THROW_ON_ERROR)
        : [];

        return View::make('npm_outdated::livewire.npm_outdated', [
            'packages' => $packages,
            'time' => $outdated?->get('time')?->value ?? null,
        ]);
    }
}
