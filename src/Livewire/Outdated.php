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
    public bool $showAge = false;

    #[Lazy]
    public function render()
    {
        // Get the data out of the Pulse data store.
        $outdated = Pulse::values('composer_outdated', ['time', 'result']);

        $packages = isset($outdated['result'])
            ? json_decode($outdated['result']->value, associative: true, flags: JSON_THROW_ON_ERROR)['installed']
            : [];

        $packages = $this->parsePackages($packages);

        return View::make('outdated::livewire.outdated', [
            'packages' => $packages,
            'time' => $outdated?->get('time')?->value ?? null,
        ]);
    }

    private function parsePackages(array $packages)
    {
        $parsed = [
            'major' => [],
            'minor' => [],
            'patch' => [],
            'other' => [],
        ];

        foreach ($packages as $package) {
            [$installedMajor, $installedMinor, $installedPatch] = $this->parseVersion($package['version']);
            [$latestMajor, $latestMinor, $latestPatch] = $this->parseVersion($package['latest']);

            if ($installedMajor === null || $latestMajor === null) {
                $parsed['other'][] = $package;
            } elseif ($installedMajor !== $latestMajor) {
                $parsed['major'][] = $package;
            } elseif ($installedMinor !== $latestMinor) {
                $parsed['minor'][] = $package;
            } elseif ($installedPatch !== $latestPatch) {
                $parsed['patch'][] = $package;
            }
        }

        // Remove empty arrays
        foreach ($parsed as $key => $value) {
            if (empty($value)) {
                unset($parsed[$key]);
            }
        }

        return $parsed;
    }

    private function parseVersion(string $versionString): array
    {
        // Handle optional 'v' followed by major, minor, and patch versions
        $pattern = '/^v?(\d+)\.(\d+)\.(\d+)$/';

        $major = $minor = $patch = null;

        if (preg_match($pattern, $versionString, $matches)) {
            $major = (int) $matches[1];
            $minor = (int) $matches[2];
            $patch = (int) $matches[3];
        }

        return [$major, $minor, $patch];
    }
}
