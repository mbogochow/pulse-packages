<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace AaronFrancis\Pulse\Outdated\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

class Outdated extends Card
{
    #[Lazy]
    public function render()
    {
        // Get the data out of the Pulse data store.
        $outdated = Pulse::values('composer_outdated', ['time', 'result']);

        $packages = isset($outdated['result'])
            ? json_decode($outdated['result']->value, associative: true, flags: JSON_THROW_ON_ERROR)['installed']
            : [];

        return View::make('outdated::livewire.outdated', [
            'packages' => $packages,
            'time' => $outdated?->get('time')?->value ?? null,
        ]);
    }
}
