<?php

namespace Bogochow\Pulse\Packages\Recorders;

use Bogochow\Pulse\Packages\ComposerVersionFilter;
use Cron\CronExpression;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Process;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use RuntimeException;

class PackagesRecorder
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
        if (!$this->shouldRun($event)) {
            return;
        }

        $this->runComposerOutdated();
        $this->runNpmOutdated();
    }

    private function shouldRun(SharedBeat $event): bool
    {
        return $this->checkDue($event) ||
               $this->pulse->values('composer_packages', ['result'])->isEmpty() ||
               $this->pulse->values('npm_packages', ['result'])->isEmpty();
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

        $this->pulse->set('composer_packages', 'result', $result->output());
        $this->pulse->set('composer_packages', 'time', now());
    }

    /**
     * @throws \JsonException
     */
    private function runNpmOutdated()
    {
        $result = Process::run('npm outdated --long --json');
        // don't check return value since it will be non-zero even on success

        json_decode($result->output(), flags: JSON_THROW_ON_ERROR);

        $this->pulse->set('npm_packages', 'result', $result->output());
        $this->pulse->set('npm_packages', 'time', now());
    }

    private function getConfigValue(string $configName, $default): mixed
    {
        return $this->config->get('pulse.recorders.' . static::class . ".$configName", $default);
    }
}
