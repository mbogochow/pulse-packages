<?php

namespace AaronFrancis\Pulse\Outdated\Commands;

use Illuminate\Console\Command;
use Laravel\Pulse\Pulse;

class OutdatedClearCommand extends Command
{
    protected $signature = 'outdated:clear';

    protected $description = 'Purge the stored outdated data. It will be rewritten if pulse:check is running.';

    public function handle(Pulse $pulse)
    {
        $pulse->purge(['composer_outdated', 'npm_outdated']);
    }
}
