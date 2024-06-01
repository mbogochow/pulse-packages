<?php

namespace AaronFrancis\Pulse\Outdated\Tests\Feature\Livewire;

use AaronFrancis\Pulse\Outdated\Livewire\ComposerOutdated;
use AaronFrancis\Pulse\Outdated\Tests\TestCase;
use Livewire\Livewire;
use Orchestra\Testbench\Attributes\WithMigration;
use PHPUnit\Framework\Attributes\Test;

#[WithMigration]
class OutdatedTest extends TestCase
{
    #[Test]
    public function it_can_set_card_props()
    {
        Livewire::test(ComposerOutdated::class, ['cols' => 4, 'rows' => 2])
            ->assertSet('cols', 4)
            ->assertSet('rows', 2);
    }
}
