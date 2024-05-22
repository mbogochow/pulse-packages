<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace AaronFrancis\Pulse\Outdated\Recorders;

use AaronFrancis\Pulse\Outdated\ComposerVersionFilter;
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
        $hasData = !$this->pulse->values('composer_outdated', ['result'])->isEmpty() ||
                    !$this->pulse->values('npm_outdated', ['result'])->isEmpty();
        if ($hasData && !$this->checkDue($event)) {
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
        $args = [
            '--direct',
            '--format=json',
            '--sort-by-age',
        ];

        $configValue = $this->getConfigValue('composer.version', null);
        if ($configValue instanceof ComposerVersionFilter) {
            $args[] = $configValue->value;
        }

        $excludedPackages = $this->getConfigValue('composer.exclude_packages', []);
        foreach ($excludedPackages as $excludedPackage) {
            $args[] = "--ignore=$excludedPackage";
        }

        $excludeDevPackages = $this->getConfigValue('composer.exclude_dev_packages', false);
        if ($excludeDevPackages) {
            $args[] = '--no-dev';
        }

        $result = Process::run('composer outdated ' . implode(' ', $args));

        if ($result->failed()) {
            throw new RuntimeException('Composer outdated failed: ' . $result->errorOutput());
        }

        json_decode($result->output(), flags: JSON_THROW_ON_ERROR);

        $this->pulse->set('composer_outdated', 'result', $result->output());
        $this->pulse->set('composer_outdated', 'time', now());
    }

    /**
     * @throws \JsonException
     */
    private function runNpmOutdated()
    {
        $npmResult = Process::run('npm outdated --long --json');

        json_decode($npmResult->output(), flags: JSON_THROW_ON_ERROR);

        $this->pulse->set('npm_outdated', 'result', $npmResult->output());
        $this->pulse->set('npm_outdated', 'time', now());
    }

    private function getConfigValue(string $configName, $default): mixed
    {
        return $this->config->get('pulse.recorders.' . static::class . ".$configName", $default);
    }
}
