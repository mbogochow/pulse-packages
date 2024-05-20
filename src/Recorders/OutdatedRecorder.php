<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace AaronFrancis\Pulse\Outdated\Recorders;

use Cron\CronExpression;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Process;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use RuntimeException;

class OutdatedRecorder
{
    /**
     * The events to listen for.
     *
     * @var class-string
     */
    public string $listen = SharedBeat::class;

    /**
     * Create a new recorder instance.
     */
    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {
        //
    }

    public function record(SharedBeat $event): void
    {
        if (!$this->checkDue($event)) {
            return;
        }

        $this->runComposerOutdated();
        $this->runNpmOutdated();
    }

    private function checkDue(SharedBeat $event): bool
    {
        $class = self::class;
        $expression = $this->config->get(sprintf('pulse.recorders.%s.cron', $class), '0 0 * * *');

        if (!CronExpression::isValidExpression($expression)) {
            throw new RuntimeException('Invalid cron expression: ' . $expression);
        }

        return (new CronExpression($expression))->isDue($event->time);
    }

    /**
     * @throws \JsonException
     */
    private function runComposerOutdated()
    {
        $result = Process::run('composer outdated -D -f json');

        if ($result->failed()) {
            throw new RuntimeException('Composer outdated failed: ' . $result->errorOutput());
        }

        json_decode($result->output(), flags: JSON_THROW_ON_ERROR);

        $this->pulse->set('composer_outdated', 'result', $result->output());
    }

    /**
     * @throws \JsonException
     */
    private function runNpmOutdated()
    {
        $npmResult = Process::run('npm outdated --long --json');

        json_decode($npmResult->output(), flags: JSON_THROW_ON_ERROR);

        $this->pulse->set('npm_outdated', 'result', $npmResult->output());
    }
}
