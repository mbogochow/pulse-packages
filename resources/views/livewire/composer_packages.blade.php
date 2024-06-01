<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Composer Dependencies">
        <x-slot:icon>
            <x-dynamic-component :component="'pulse::icons.sparkles'" />
        </x-slot:icon>
        @if ($time)
            <x-slot:details>
                Last updated: {{ $time }}
            </x-slot:details>
        @endif
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if (!count($packages))
            <x-pulse::no-results />
        @else
            @foreach ($packages as $change => $changePackages)
                <h1 class="font-bold mt-2">{{ ucfirst($change)  }} Changes</h1>
                <x-pulse::table>
                    <colgroup>
                        <col style="width: 50%" />
                        <col />
                        <col />
                        @if ($showAge)
                            <col />
                        @endif
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Package</x-pulse::th>
                            <x-pulse::th class="text-right">Installed</x-pulse::th>
                            <x-pulse::th class="text-right">Available</x-pulse::th>
                            @if ($showAge)
                                <x-pulse::th class="text-right">Release Age</x-pulse::th>
                            @endif
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                    @foreach ($changePackages as $package)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $package['name'] }}">
                            <x-pulse::td class="max-w-[1px]">
                            <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                {{ $package['name'] }}
                            </code>
                                @isset($package['source'])
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate" title="">
                                        <a href='{{ $package['source'] }}'>
                                            {{ str($package['source'])->after('://') }}
                                        </a>
                                    </p>
                                @endisset
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $package['version'] }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $package['latest'] }}
                            </x-pulse::td>
                            @if ($showAge)
                                <x-pulse::td date class="text-gray-700 dark:text-gray-300 font-bold">
                                    {{ $package['release-age'] }}
                                </x-pulse::td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </x-pulse::table>
            @endforeach
        @endif
    </x-pulse::scroll>
</x-pulse::card>
