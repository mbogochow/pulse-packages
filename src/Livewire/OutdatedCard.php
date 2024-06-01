<?php

namespace AaronFrancis\Pulse\Outdated\Livewire;

use Laravel\Pulse\Livewire\Card;

class OutdatedCard extends Card
{
    protected string $installedKey = 'version';
    protected string $latestKey = 'latest';

    protected function parsePackages(array $packages): array
    {
        $parsed = [
            'major' => [],
            'minor' => [],
            'patch' => [],
            'other' => [],
        ];

        foreach ($packages as $key => $package) {
            [$installedMajor, $installedMinor, $installedPatch] = $this->parseVersion($package[$this->installedKey]);
            [$latestMajor, $latestMinor, $latestPatch] = $this->parseVersion($package[$this->latestKey]);

            if ($installedMajor === null || $latestMajor === null) {
                $parsed['other'][$key] = $package;
            } elseif ($installedMajor !== $latestMajor) {
                $parsed['major'][$key] = $package;
            } elseif ($installedMinor !== $latestMinor) {
                $parsed['minor'][$key] = $package;
            } elseif ($installedPatch !== $latestPatch) {
                $parsed['patch'][$key] = $package;
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
