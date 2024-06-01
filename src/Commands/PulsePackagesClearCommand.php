<?php

namespace Bogochow\Pulse\Packages\Commands;

use Illuminate\Console\Command;
use Laravel\Pulse\Pulse;

class PulsePackagesClearCommand extends Command
{
    protected $signature = 'pulse:clear:packages';

    protected $description = 'Purge the stored packages data. It will be rewritten if pulse:check is running.';

    public function handle(Pulse $pulse)
    {
        $pulse->purge(['composer_packages', 'npm_packages']);
    }
}
