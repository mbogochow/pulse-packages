# Composer and NPM packages status card for Laravel Pulse

This card will show you outdated and vulnerable Composer and NPM dependencies.

Based on [aarondfrancis/pulse-outdated](https://github.com/aarondfrancis/pulse-outdated) and [hungthai1401/vulnerable](https://github.com/hungthai1401/vulnerable).

## Installation

Require the package with Composer:

```shell
composer require mbogochow/pulse-packages
```

## Register the recorder

The Composer and NPM dependencies will be checked automatically if there is no recorded data and will be updated once per day. To run the checks you must add the `PackagesRecorder` to the `pulse.php` file.

```diff
return [
    // ...

    'recorders' => [
+       \Bogochow\Pulse\Packages\Recorders\PackagesRecorder::class => [
+           'composer' => [
+               'version' => ComposerVersionFilter::MINOR_ONLY,
+               'exclude_dev_packages' => false,
+               'exclude_packages' => [
+                   'roave/security-advisories',
+               ],
+           ],
+       ],
    ]
]
```

You also need to be running [the `pulse:check` command](https://laravel.com/docs/11.x/pulse#dashboard-cards).

## Add to your dashboard

To add the card to the Pulse dashboard, you must first [publish the vendor view](https://laravel.com/docs/11.x/pulse#dashboard-customization).

Then, you can modify the `dashboard.blade.php` file:

```diff
<x-pulse>
+    <livewire:composer_packages cols='4' rows='2' />

+    <livewire:npm_packages cols='4' rows='2' />

    <livewire:pulse.servers cols="full" />

    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:pulse.queues cols="4" />

    <livewire:pulse.cache cols="4" />

    <livewire:pulse.slow-queries cols="8" />

    <livewire:pulse.exceptions cols="6" />

    <livewire:pulse.slow-requests cols="6" />

    <livewire:pulse.slow-jobs cols="6" />

    <livewire:pulse.slow-outgoing-requests cols="6" />

</x-pulse>
```

That's it!
