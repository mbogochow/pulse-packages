<?php

namespace Bogochow\Pulse\Packages\Tests\Feature\Livewire;

use Bogochow\Pulse\Packages\Livewire\ComposerPackages;
use Bogochow\Pulse\Packages\Tests\TestCase;
use Livewire\Livewire;
use Orchestra\Testbench\Attributes\WithMigration;
use PHPUnit\Framework\Attributes\Test;

#[WithMigration]
class PackagesTest extends TestCase
{
    #[Test]
    public function it_can_set_card_props()
    {
        Livewire::test(ComposerPackages::class, ['cols' => 4, 'rows' => 2])
            ->assertSet('cols', 4)
            ->assertSet('rows', 2);
    }
}
